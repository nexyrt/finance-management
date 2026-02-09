<?php

namespace App\Livewire\Invoices;

use App\Models\Client;
use App\Models\Service;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;
use TallStackUi\Traits\Interactions;

class Edit extends Component
{
    use WithFileUploads, Interactions;

    public Invoice $invoice;

    // Invoice data - will be synced from Alpine
    public $invoiceData = [
        'invoice_number' => '',
        'client_id' => null,
        'issue_date' => null,
        'due_date' => null,
    ];

    public $faktur;
    public $fakturName;

    // Items array - will be synced from Alpine
    public $items = [];

    // Discount - will be synced from Alpine
    public $discount = [
        'type' => 'fixed',
        'value' => 0,
        'reason' => '',
    ];

    public function mount(Invoice $invoice)
    {
        $this->invoice = $invoice->load(['items.client']);
    }

    public function update()
    {
        $this->validate([
            'invoiceData.client_id' => 'required|exists:clients,id',
            'invoiceData.issue_date' => 'required|date',
            'invoiceData.due_date' => 'required|date|after_or_equal:invoiceData.issue_date',
            'items' => 'required|array|min:1',
            'items.*.client_id' => 'required|exists:clients,id',
            'items.*.service_name' => 'required|string|max:255',
            'items.*.quantity' => 'required',
            'items.*.unit' => 'nullable|string|max:20',
            'items.*.unit_price' => 'required',
            'discount.type' => 'in:fixed,percentage',
            'discount.value' => 'nullable|numeric|min:0',
            'faktur' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'fakturName' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $parsedItems = [];
            $subtotal = 0;

            foreach ($this->items as $item) {
                $unitPrice = $this->parseAmount($item['unit_price']);
                $quantity = $this->parseQuantity($item['quantity']);
                $amount = $unitPrice * $quantity;
                $cogsAmount = $this->parseAmount($item['cogs_amount'] ?? '0');
                $isTaxDeposit = $item['is_tax_deposit'] ?? false;
                $unit = $item['unit'] ?? 'pcs';

                $parsedItems[] = [
                    'client_id' => $item['client_id'],
                    'service_name' => $item['service_name'],
                    'quantity' => $quantity,
                    'unit' => $unit,
                    'unit_price' => $unitPrice,
                    'amount' => $amount,
                    'cogs_amount' => $cogsAmount,
                    'is_tax_deposit' => $isTaxDeposit,
                ];

                $subtotal += $amount;
            }

            // Calculate discount
            $discountAmount = 0;
            $discountValue = 0;
            if ($this->discount['type'] === 'fixed') {
                $discountValue = $this->discount['value'];
                $discountAmount = $this->discount['value'];
            } else {
                $discountValue = $this->discount['value'];
                $discountAmount = ($subtotal * $this->discount['value']) / 100;
            }

            $totalAmount = max(0, $subtotal - $discountAmount);

            $updateData = [
                'billed_to_id' => $this->invoiceData['client_id'],
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'discount_type' => $this->discount['type'] ?? 'fixed',
                'discount_value' => $discountValue,
                'discount_reason' => $this->discount['reason'],
                'total_amount' => $totalAmount,
                'issue_date' => $this->invoiceData['issue_date'],
                'due_date' => $this->invoiceData['due_date'],
            ];

            if ($this->faktur) {
                if ($this->invoice->faktur) {
                    Storage::disk('public')->delete($this->invoice->faktur);
                }
                $customName = $this->fakturName ? $this->fakturName : $this->faktur->getClientOriginalName();
                $extension = $this->faktur->getClientOriginalExtension();
                $fileName = pathinfo($customName, PATHINFO_FILENAME) . '.' . $extension;
                $updateData['faktur'] = $this->faktur->storeAs('invoices/fakturs', $fileName, 'public');
            }

            // Update invoice
            $this->invoice->update($updateData);

            // Delete old items and create new ones
            $this->invoice->items()->delete();

            foreach ($parsedItems as $itemData) {
                InvoiceItem::create([
                    'invoice_id' => $this->invoice->id,
                    'client_id' => $itemData['client_id'],
                    'service_name' => $itemData['service_name'],
                    'quantity' => $itemData['quantity'],
                    'unit' => $itemData['unit'],
                    'unit_price' => $itemData['unit_price'],
                    'amount' => $itemData['amount'],
                    'cogs_amount' => $itemData['cogs_amount'],
                    'is_tax_deposit' => $itemData['is_tax_deposit'],
                ]);
            }

            // Evaluate status based on payments
            $this->evaluateAndUpdateStatus();

            DB::commit();

            $this->toast()->success(__('invoice.updated_successfully'))->send();

            return $this->redirect(route('invoices.index'), navigate: true);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            throw $e; // Let Livewire handle validation errors
        } catch (\Exception $e) {
            DB::rollBack();

            // Comprehensive logging with context
            \Log::error('Failed to update invoice', [
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'invoice_data' => [
                    'invoice_id' => $this->invoice->id ?? null,
                    'invoice_number' => $this->invoice->invoice_number ?? null,
                    'client_id' => $this->invoiceData['client_id'] ?? null,
                    'items_count' => count($this->items ?? []),
                ],
                'parsed_items_sample' => isset($parsedItems) ? array_slice($parsedItems, 0, 2) : [],
            ]);

            // Build detailed user-friendly error message
            $errorMessage = __('invoice.update_failed');

            // Add specific error details
            if (strpos($e->getMessage(), 'SQLSTATE') !== false) {
                $errorMessage .= ' ' . __('common.database_error') . ': ' . $e->getMessage();
            } elseif (strpos($e->getMessage(), 'column') !== false || strpos($e->getMessage(), 'Column') !== false) {
                $errorMessage .= ' ' . __('common.missing_required_field') . ': ' . $e->getMessage();
            } elseif (strpos($e->getMessage(), 'Undefined') !== false) {
                $errorMessage .= ' ' . __('common.data_issue') . ': ' . $e->getMessage();
            } else {
                $errorMessage .= ' ' . $e->getMessage();
            }

            // Always show file and line in production for debugging
            $errorMessage .= "\n\n" . __('common.error_location') . ': ' . basename($e->getFile()) . ':' . $e->getLine();

            $this->toast()->error($errorMessage)->send();
        }
    }

    private function evaluateAndUpdateStatus()
    {
        $this->invoice->refresh();
        $totalPaid = $this->invoice->payments()->sum('amount');
        $totalAmount = $this->invoice->total_amount;
        $dueDate = \Carbon\Carbon::parse($this->invoice->due_date);

        // Fully paid
        if ($totalPaid >= $totalAmount && $totalPaid > 0) {
            $this->invoice->update(['status' => 'paid']);
            return;
        }

        // Partial payment
        if ($totalPaid > 0 && $totalPaid < $totalAmount) {
            $this->invoice->update(['status' => 'partially_paid']);
            return;
        }

        // No payment
        if ($totalPaid == 0) {
            if ($this->invoice->status === 'draft') {
                $this->invoice->update(['status' => $dueDate->isPast() ? 'overdue' : 'draft']);
            } else {
                $this->invoice->update(['status' => $dueDate->isPast() ? 'overdue' : $this->invoice->status]);
            }
        }
    }

    private function parseAmount($value): int
    {
        if (empty($value))
            return 0;
        return (int) preg_replace('/[^0-9]/', '', $value);
    }

    private function parseQuantity($value): float
    {
        if (empty($value))
            return 0;

        // Convert Indonesian format (2.828,93) to standard float (2828.93)
        // Remove thousand separators (dots)
        $value = str_replace('.', '', $value);
        // Replace decimal comma with dot
        $value = str_replace(',', '.', $value);

        return (float) $value;
    }

    #[Computed]
    public function existingInvoiceData()
    {
        // Load client jika belum di-load
        if (!$this->invoice->relationLoaded('client')) {
            $this->invoice->load('client');
        }

        return [
            'invoice_number' => $this->invoice->invoice_number,
            'client_id' => $this->invoice->billed_to_id,
            'client_name' => $this->invoice->client->name ?? '',
            'issue_date' => $this->invoice->issue_date->format('Y-m-d'),
            'due_date' => $this->invoice->due_date->format('Y-m-d'),
        ];
    }

    #[Computed]
    public function existingItems()
    {
        return $this->invoice->items->map(function ($item, $index) {
            return [
                'id' => $index + 1,
                'client_id' => $item->client_id,
                'client_name' => $item->client->name,
                'service_name' => $item->service_name,
                'quantity' => number_format($item->quantity, 3, ',', '.'),
                'unit' => $item->unit ?? 'pcs',
                'unit_price' => number_format($item->unit_price, 0, ',', '.'),
                'amount' => $item->amount,
                'cogs_amount' => number_format($item->cogs_amount, 0, ',', '.'),
                'profit' => $item->amount - $item->cogs_amount,
                'is_tax_deposit' => $item->is_tax_deposit,
            ];
        })->toArray();
    }

    #[Computed]
    public function existingDiscount()
    {
        return [
            'type' => $this->invoice->discount_type,
            'value' => $this->invoice->discount_type === 'percentage'
                ? $this->invoice->discount_value
                : number_format($this->invoice->discount_value, 0, ',', '.'),
            'reason' => $this->invoice->discount_reason ?? '',
        ];
    }

    #[Computed]
    public function clients()
    {
        return Client::where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'logo'])
            ->toArray();
    }

    #[Computed]
    public function services()
    {
        return Service::orderBy('name')
            ->get(['id', 'name', 'price', 'type'])
            ->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'price' => $service->price,
                    'type' => $service->type,
                    'formatted_price' => 'Rp ' . number_format($service->price, 0, ',', '.')
                ];
            })
            ->toArray();
    }

    public function render()
    {
        return view('livewire.invoices.edit');
    }
}