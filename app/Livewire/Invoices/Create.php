<?php

namespace App\Livewire\Invoices;

use App\Models\Client;
use App\Models\Service;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    // Invoice data
    public $invoice = [
        'invoice_number' => '',
        'client_id' => null,
        'issue_date' => null,
        'due_date' => null,
    ];

    public $faktur;
    public $fakturName;

    // Items untuk save
    public $items = [];
    public $discount = [
        'type' => 'fixed',
        'value' => 0,
        'reason' => '',
        'amount' => 0
    ];

    public function save()
    {
        $this->validate([
            'invoice.client_id' => 'required|exists:clients,id',
            'invoice.issue_date' => 'required|date',
            'invoice.due_date' => 'required|date|after_or_equal:invoice.issue_date',
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

            $invoiceNumber = $this->invoice['invoice_number'];

            $fakturPath = null;
            if ($this->faktur) {
                $customName = $this->fakturName ? $this->fakturName : $this->faktur->getClientOriginalName();
                $extension = $this->faktur->getClientOriginalExtension();
                $fileName = pathinfo($customName, PATHINFO_FILENAME) . '.' . $extension;
                $fakturPath = $this->faktur->storeAs('invoices/fakturs', $fileName, 'public');
            }

            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'billed_to_id' => $this->invoice['client_id'],
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'discount_type' => $this->discount['type'] ?? 'fixed',
                'discount_value' => $discountValue,
                'discount_reason' => $this->discount['reason'],
                'total_amount' => $totalAmount,
                'issue_date' => $this->invoice['issue_date'],
                'due_date' => $this->invoice['due_date'],
                'status' => 'draft',
                'faktur' => $fakturPath,
            ]);

            foreach ($parsedItems as $itemData) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
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

            DB::commit();

            session()->flash('success', "Invoice {$invoiceNumber} created successfully!");

            $this->reset(['invoice', 'items', 'discount']);

            return $this->redirect(request()->header('Referer'), navigate: true);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to create invoice: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            // Show detailed error message to user
            $errorMessage = 'Failed to create invoice: ' . $e->getMessage();
            if (config('app.debug')) {
                $errorMessage .= "\n\nFile: " . $e->getFile() . ':' . $e->getLine();
            }

            session()->flash('error', $errorMessage);
        }
    }

    #[Computed]
    public function maxInvoiceSequence()
    {
        $date = now();

        $invoices = Invoice::whereYear('issue_date', $date->year)
            ->whereMonth('issue_date', $date->month)
            ->pluck('invoice_number');

        $maxSequence = 0;
        foreach ($invoices as $invoiceNumber) {
            if (preg_match('/INV\/(\d+)\/KSN\/\d{2}\.\d{2}/', $invoiceNumber, $matches)) {
                $sequence = (int) $matches[1];
                $maxSequence = max($maxSequence, $sequence);
            }
        }

        return $maxSequence;
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
        return view('livewire.invoices.create');
    }
}