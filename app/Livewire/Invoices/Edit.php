<?php

namespace App\Livewire\Invoices;

use App\Models\Invoice;
use App\Models\Client;
use App\Models\Service;
use App\Models\InvoiceItem;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use TallStackUi\Traits\Interactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Edit extends Component
{
    use Interactions;

    public ?Invoice $invoice = null;
    public bool $showModal = false;

    // Invoice properties
    public string $invoice_number = '';
    public string $billed_to_id = '';
    public string $issue_date = '';
    public string $due_date = '';
    public string $discount_type = 'fixed';
    public $discount_value = null;
    public string $discount_reason = '';

    // Invoice items
    public array $items = [];
    public int $itemCounter = 0;

    // Calculated values (read-only)
    public int $subtotal = 0;
    public int $discount_amount = 0;
    public int $total_amount = 0;

    // Status preview properties
    public string $currentStatus = '';
    public string $previewStatus = '';
    public bool $statusWillChange = false;
    public string $statusChangeMessage = '';

    protected array $rules = [
        'invoice_number' => 'required|string',
        'billed_to_id' => 'required|exists:clients,id',
        'issue_date' => 'required|date',
        'due_date' => 'required|date|after_or_equal:issue_date',
        'discount_type' => 'in:fixed,percentage',
        'discount_value' => 'nullable|numeric|min:0',
        'discount_reason' => 'nullable|string|max:255',
        'items' => 'required|array|min:1',
        'items.*.client_id' => 'required|exists:clients,id',
        'items.*.service_name' => 'required|string|max:255',
        'items.*.quantity' => 'required|integer|min:1',
        'items.*.unit_price' => 'required|numeric|min:0',
    ];

    protected array $messages = [
        'items.required' => 'Minimal harus ada 1 item invoice',
        'items.*.client_id.required' => 'Klien harus dipilih untuk setiap item',
        'items.*.service_name.required' => 'Nama layanan wajib diisi',
        'items.*.quantity.required' => 'Kuantitas wajib diisi',
        'items.*.unit_price.required' => 'Harga satuan wajib diisi',
    ];

    #[On('edit-invoice')]
    public function edit(int $invoiceId): void
    {
        $this->invoice = Invoice::with(['client', 'items.client', 'payments'])->find($invoiceId);
        
        if (!$this->invoice) {
            $this->toast()->error('Error', 'Invoice tidak ditemukan')->send();
            return;
        }

        // ✅ EDIT SEMUA STATUS - TIDAK ADA RESTRICTION
        $this->loadInvoiceData();
        $this->currentStatus = $this->invoice->status;
        $this->evaluateStatusPreview();
        $this->showModal = true;
    }

    private function loadInvoiceData(): void
    {
        // Load basic invoice data
        $this->invoice_number = $this->invoice->invoice_number;
        $this->billed_to_id = (string) $this->invoice->billed_to_id;
        $this->issue_date = $this->invoice->issue_date->format('Y-m-d');
        $this->due_date = $this->invoice->due_date->format('Y-m-d');
        $this->discount_type = $this->invoice->discount_type;
        $this->discount_value = $this->invoice->discount_value > 0 ? $this->invoice->discount_value : null;
        $this->discount_reason = $this->invoice->discount_reason ?? '';

        // Load items
        $this->items = [];
        $this->itemCounter = 0;
        
        foreach ($this->invoice->items as $item) {
            $this->items[] = [
                'id' => ++$this->itemCounter,
                'item_id' => $item->id,
                'client_id' => (string) $item->client_id,
                'service_name' => $item->service_name,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'amount' => $item->amount,
            ];
        }

        $this->calculateTotals();
    }

    /**
     * ✅ CORE BUSINESS LOGIC: Status Evaluation - SAFE VERSION
     */
    private function evaluateInvoiceStatus(?Invoice $invoice = null): string
    {
        try {
            $targetInvoice = $invoice ?? $this->invoice;
            
            if (!$targetInvoice) return 'draft';

            $totalPaid = $targetInvoice->payments()->sum('amount') ?? 0;
            $totalAmount = $invoice ? $invoice->total_amount : $this->total_amount; 
            $dueDate = $invoice ? $invoice->due_date : now()->parse($this->due_date);

            // 1. Paid (including overpaid)
            if ($totalPaid >= $totalAmount && $totalPaid > 0) {
                return 'paid';
            }

            // 2. Partially paid
            if ($totalPaid > 0 && $totalPaid < $totalAmount) {
                return 'partially_paid';
            }

            // 3. No payment yet
            if ($totalPaid == 0) {
                return $dueDate->isPast() ? 'overdue' : 'sent';
            }

            return 'draft'; // Fallback
        } catch (\Exception $e) {
            \Log::error('Error evaluating invoice status: ' . $e->getMessage());
            return $this->currentStatus ?? 'draft';
        }
    }

    /**
     * ✅ REAL-TIME STATUS PREVIEW - SAFE VERSION
     */
    private function evaluateStatusPreview(): void
    {
        try {
            $this->previewStatus = $this->evaluateInvoiceStatus();
            $this->statusWillChange = $this->currentStatus !== $this->previewStatus;
            
            if ($this->statusWillChange) {
                $this->statusChangeMessage = $this->generateStatusChangeMessage(
                    $this->currentStatus, 
                    $this->previewStatus
                );
            } else {
                $this->statusChangeMessage = '';
            }
        } catch (\Exception $e) {
            \Log::error('Error evaluating status preview: ' . $e->getMessage());
            $this->previewStatus = $this->currentStatus;
            $this->statusWillChange = false;
            $this->statusChangeMessage = '';
        }
    }

    /**
     * ✅ STATUS CHANGE MESSAGES - SAFE VERSION
     */
    private function generateStatusChangeMessage(string $oldStatus, string $newStatus): string
    {
        $statusLabels = [
            'draft' => 'Draft',
            'sent' => 'Terkirim',
            'paid' => 'Lunas',
            'partially_paid' => 'Sebagian Dibayar',
            'overdue' => 'Terlambat'
        ];

        $messages = [
            'paid' => [
                'partially_paid' => 'Invoice berubah dari LUNAS menjadi SEBAGIAN DIBAYAR karena total amount bertambah',
                'sent' => 'Invoice berubah dari LUNAS menjadi TERKIRIM karena total amount bertambah dan due date belum lewat',
                'overdue' => 'Invoice berubah dari LUNAS menjadi TERLAMBAT karena total amount bertambah dan due date sudah lewat',
            ],
            'partially_paid' => [
                'paid' => 'Invoice berubah menjadi LUNAS karena pembayaran sudah mencukupi total yang baru',
                'sent' => 'Invoice berubah menjadi TERKIRIM karena belum ada pembayaran',
                'overdue' => 'Invoice berubah menjadi TERLAMBAT karena due date sudah lewat',
            ],
            'sent' => [
                'paid' => 'Invoice berubah menjadi LUNAS karena pembayaran sudah mencukupi',
                'partially_paid' => 'Invoice berubah menjadi SEBAGIAN DIBAYAR',
                'overdue' => 'Invoice berubah menjadi TERLAMBAT karena due date sudah lewat',
            ],
            'overdue' => [
                'paid' => 'Invoice berubah menjadi LUNAS',
                'partially_paid' => 'Invoice berubah menjadi SEBAGIAN DIBAYAR',
                'sent' => 'Invoice berubah menjadi TERKIRIM karena due date diperpanjang',
            ],
            'draft' => [
                'sent' => 'Invoice berubah menjadi TERKIRIM',
                'paid' => 'Invoice berubah menjadi LUNAS',
                'partially_paid' => 'Invoice berubah menjadi SEBAGIAN DIBAYAR',
                'overdue' => 'Invoice berubah menjadi TERLAMBAT',
            ]
        ];

        // Safe array access with fallback
        $oldLabel = $statusLabels[$oldStatus] ?? ucfirst($oldStatus);
        $newLabel = $statusLabels[$newStatus] ?? ucfirst($newStatus);
        
        return $messages[$oldStatus][$newStatus] ?? 
               "Status berubah dari {$oldLabel} menjadi {$newLabel}";
    }

    /**
     * ✅ OVERPAYMENT CALCULATION - SAFE VERSION
     */
    #[Computed]
    public function overpaymentAmount(): int
    {
        if (!$this->invoice || !$this->invoice->payments) return 0;
        
        try {
            $totalPaid = $this->invoice->payments()->sum('amount');
            return max(0, $totalPaid - $this->total_amount);
        } catch (\Exception $e) {
            \Log::error('Error calculating overpayment amount: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * ✅ PAYMENT SUMMARY FOR PREVIEW - SAFE VERSION
     */
    #[Computed]
    public function paymentSummary(): array
    {
        if (!$this->invoice) {
            return ['paid' => 0, 'remaining' => 0, 'percentage' => 0, 'overpaid' => 0];
        }

        try {
            $totalPaid = $this->invoice->payments()->sum('amount') ?? 0;
            $remaining = max(0, $this->total_amount - $totalPaid);
            $percentage = $this->total_amount > 0 ? ($totalPaid / $this->total_amount) * 100 : 0;
            $overpaid = max(0, $totalPaid - $this->total_amount);

            return [
                'paid' => $totalPaid,
                'remaining' => $remaining,
                'percentage' => min($percentage, 100), // Cap at 100%
                'overpaid' => $overpaid,
            ];
        } catch (\Exception $e) {
            \Log::error('Error calculating payment summary: ' . $e->getMessage());
            return ['paid' => 0, 'remaining' => 0, 'percentage' => 0, 'overpaid' => 0];
        }
    }

    public function resetData(): void
    {
        $this->invoice = null;
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->invoice_number = '';
        $this->billed_to_id = '';
        $this->issue_date = '';
        $this->due_date = '';
        $this->discount_type = 'fixed';
        $this->discount_value = null;
        $this->discount_reason = '';
        $this->items = [];
        $this->itemCounter = 0;
        $this->subtotal = 0;
        $this->discount_amount = 0;
        $this->total_amount = 0;
        $this->currentStatus = '';
        $this->previewStatus = '';
        $this->statusWillChange = false;
        $this->statusChangeMessage = '';
        $this->resetValidation();
    }

    /**
     * ✅ SAFE MODAL CLOSE - Dipanggil dari Alpine/Frontend
     */
    public function closeModal(): void
    {
        try {
            $this->resetData();
        } catch (\Exception $e) {
            \Log::error('Error closing edit modal: ' . $e->getMessage());
            // Force reset even if error occurs
            $this->showModal = false;
            $this->invoice = null;
        }
    }

    public function addItem(): void
    {
        $this->items[] = [
            'id' => ++$this->itemCounter,
            'item_id' => null,
            'client_id' => $this->billed_to_id,
            'service_name' => '',
            'quantity' => 1,
            'unit_price' => null,
            'amount' => 0,
        ];
    }

    public function removeItem($itemIdentifier): void
    {
        $this->items = array_filter($this->items, fn($item) => ($item['id'] ?? null) !== (int)$itemIdentifier);
        $this->items = array_values($this->items);
        
        if (empty($this->items)) {
            $this->addItem();
        }
        
        $this->calculateTotals();
        $this->evaluateStatusPreview(); // ✅ Update preview after item change
    }

    public function loadService(int $itemIndex, int $serviceId): void
    {
        $service = Service::find($serviceId);
        if ($service && isset($this->items[$itemIndex])) {
            $this->items[$itemIndex]['service_name'] = $service->name;
            $this->items[$itemIndex]['unit_price'] = $service->price;
            $this->calculateItemAmount($itemIndex);
        }
    }

    public function updatedItems(): void
    {
        foreach ($this->items as $index => $item) {
            $this->calculateItemAmount($index);
        }
        $this->calculateTotals();
        $this->evaluateStatusPreview(); // ✅ Update preview
    }

    public function updatedBilledToId(): void
    {
        foreach ($this->items as $index => $item) {
            $this->items[$index]['client_id'] = $this->billed_to_id;
        }
    }

    public function updatedDiscountValue(): void
    {
        $this->calculateTotals();
        $this->evaluateStatusPreview(); // ✅ Update preview
    }

    public function updatedDiscountType(): void
    {
        $this->discount_value = null;
        $this->calculateTotals();
        $this->evaluateStatusPreview(); // ✅ Update preview
    }

    public function updatedDueDate(): void
    {
        $this->evaluateStatusPreview(); // ✅ Update preview when due date changes
    }

    private function calculateItemAmount(int $index): void
    {
        if (isset($this->items[$index])) {
            $quantity = (int) ($this->items[$index]['quantity'] ?? 1);
            $unitPrice = (int) ($this->items[$index]['unit_price'] ?? 0);
            $this->items[$index]['amount'] = $quantity * $unitPrice;
        }
    }

    private function calculateTotals(): void
    {
        $this->subtotal = array_sum(array_column($this->items, 'amount'));
        
        if ($this->discount_value > 0) {
            if ($this->discount_type === 'percentage') {
                $this->discount_amount = (int) (($this->subtotal * $this->discount_value) / 100);
            } else {
                $this->discount_amount = (int) $this->discount_value;
            }
            $this->discount_amount = min($this->discount_amount, $this->subtotal);
        } else {
            $this->discount_amount = 0;
        }
        
        $this->total_amount = $this->subtotal - $this->discount_amount;
    }

    /**
     * ✅ ENHANCED SAVE WITH AUTO STATUS RECALCULATION
     */
    public function save(): void
    {
        // Custom validation for unique invoice number (excluding current invoice)
        $this->rules['invoice_number'] = 'required|string|unique:invoices,invoice_number,' . $this->invoice->id;
        
        $this->validate();

        try {
            DB::transaction(function () {
                $oldStatus = $this->invoice->status;
                
                // 1. Update invoice data (WITHOUT status)
                $this->invoice->update([
                    'invoice_number' => $this->invoice_number,
                    'billed_to_id' => $this->billed_to_id,
                    'subtotal' => $this->subtotal,
                    'discount_type' => $this->discount_type,
                    'discount_value' => $this->discount_value ? (int) $this->discount_value : 0,
                    'discount_amount' => $this->discount_amount,
                    'discount_reason' => $this->discount_reason ?: null,
                    'total_amount' => $this->total_amount,
                    'issue_date' => $this->issue_date,
                    'due_date' => $this->due_date,
                ]);

                // 2. Update/Create/Delete items
                $this->updateInvoiceItems();

                // 3. ✅ AUTO-RECALCULATE STATUS
                $newStatus = $this->evaluateInvoiceStatus($this->invoice->fresh());
                $this->invoice->update(['status' => $newStatus]);

                // 4. ✅ LOG STATUS CHANGE
                if ($oldStatus !== $newStatus) {
                    Log::info("Invoice {$this->invoice->invoice_number} status changed", [
                        'old_status' => $oldStatus,
                        'new_status' => $newStatus,
                        'reason' => 'manual_edit',
                        'user_id' => auth()->id(),
                    ]);
                }

                // 5. ✅ SUCCESS MESSAGE WITH STATUS INFO
                $invoiceNumber = $this->invoice_number;
                $message = "Invoice {$invoiceNumber} berhasil diupdate";
                
                if ($oldStatus !== $newStatus) {
                    $message .= ". Status berubah dari " . ucfirst($oldStatus) . " menjadi " . ucfirst($newStatus);
                }

                $this->resetData();
                $this->toast()->success('Berhasil', $message)->send();
                $this->dispatch('invoice-updated');
            });

        } catch (\Exception $e) {
            $this->toast()->error('Error', 'Gagal mengupdate invoice: ' . $e->getMessage())->send();
        }
    }

    private function updateInvoiceItems(): void
    {
        $existingItemIds = $this->invoice->items->pluck('id')->toArray();
        $updatedItemIds = [];

        // Update/Create items
        foreach ($this->items as $item) {
            if (isset($item['item_id']) && $item['item_id']) {
                // Update existing item
                $invoiceItem = InvoiceItem::find($item['item_id']);
                if ($invoiceItem) {
                    $invoiceItem->update([
                        'client_id' => $item['client_id'],
                        'service_name' => $item['service_name'],
                        'quantity' => $item['quantity'],
                        'unit_price' => (int) $item['unit_price'],
                        'amount' => $item['amount'],
                    ]);
                    $updatedItemIds[] = $item['item_id'];
                }
            } else {
                // Create new item
                $newItem = InvoiceItem::create([
                    'invoice_id' => $this->invoice->id,
                    'client_id' => $item['client_id'],
                    'service_name' => $item['service_name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => (int) $item['unit_price'],
                    'amount' => $item['amount'],
                ]);
                $updatedItemIds[] = $newItem->id;
            }
        }

        // Delete removed items
        $itemsToDelete = array_diff($existingItemIds, $updatedItemIds);
        if (!empty($itemsToDelete)) {
            InvoiceItem::whereIn('id', $itemsToDelete)->delete();
        }
    }

    public function getClientsProperty()
    {
        return Client::select('id', 'name', 'type')
            ->where('status', 'Active')
            ->orderBy('type')
            ->orderBy('name')
            ->get()
            ->map(fn($client) => [
                'label' => ($client->type === 'individual' ? '👤 ' : '🏢 ') . $client->name,
                'value' => $client->id,
                'description' => ucfirst($client->type),
            ]);
    }

    public function getServicesProperty()
    {
        return Service::select('id', 'name', 'price', 'type')
            ->orderBy('type')
            ->orderBy('name')
            ->get()
            ->map(fn($service) => [
                'label' => $service->name,
                'value' => $service->id,
                'description' => $service->type . ' - Rp ' . number_format($service->price, 0, ',', '.'),
            ]);
    }

    public function render()
    {
        return view('livewire.invoices.edit');
    }
}