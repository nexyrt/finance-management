<?php

namespace App\Livewire\Invoices;

use App\Models\Invoice;
use App\Models\Client;
use App\Models\Service;
use App\Models\InvoiceItem;
use Livewire\Component;
use Livewire\Attributes\On;
use TallStackUi\Traits\Interactions;
use Illuminate\Support\Str;

class Create extends Component
{
    use Interactions;

    public bool $showModal = false;

    // Invoice properties
    public string $invoice_number = '';
    public string $billed_to_id = '';
    public string $issue_date = '';
    public string $due_date = '';
    public string $discount_type = 'fixed';
    public $discount_value = null; // For WireUI currency
    public string $discount_reason = '';

    // Invoice items
    public array $items = [];
    public int $itemCounter = 0;

    // Calculated values (read-only)
    public int $subtotal = 0;
    public int $discount_amount = 0;
    public int $total_amount = 0;

    protected array $rules = [
        'invoice_number' => 'required|string|unique:invoices,invoice_number',
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

    public function mount(): void
    {
        $this->generateInvoiceNumber();
        $this->issue_date = now()->format('Y-m-d');
        $this->due_date = now()->addDays(30)->format('Y-m-d');
        $this->addItem(); // Add first item by default
    }

    #[On('create-invoice')]
    public function create(): void
    {
        $this->resetForm();
        $this->generateInvoiceNumber();
        $this->showModal = true;
    }

    public function resetData(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->invoice_number = '';
        $this->billed_to_id = '';
        $this->issue_date = now()->format('Y-m-d');
        $this->due_date = now()->addDays(30)->format('Y-m-d');
        $this->discount_type = 'fixed';
        $this->discount_value = null;
        $this->discount_reason = '';
        $this->items = [];
        $this->itemCounter = 0;
        $this->subtotal = 0;
        $this->discount_amount = 0;
        $this->total_amount = 0;
        $this->resetValidation();
        
        // Generate new invoice number
        $this->generateInvoiceNumber();
        
        // Add fresh item with proper structure
        $this->addItem();
    }

    private function generateInvoiceNumber(): void
    {
        $date = now()->format('Ymd');
        $lastInvoice = Invoice::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastInvoice ? (int) substr($lastInvoice->invoice_number, -3) + 1 : 1;
        $this->invoice_number = 'INV-' . $date . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }

    public function addItem(): void
    {
        $this->items[] = [
            'id' => ++$this->itemCounter,
            'client_id' => $this->billed_to_id,
            'service_name' => '',
            'quantity' => 1,
            'unit_price' => null,
            'amount' => 0,
        ];
    }

    public function removeItem($itemIdentifier): void
    {
        // Handle both ID-based and index-based removal
        if (is_numeric($itemIdentifier)) {
            // Try to remove by ID first
            $this->items = array_filter($this->items, fn($item) => ($item['id'] ?? null) !== (int)$itemIdentifier);
            
            // If no item was removed (ID not found), treat as index
            if (count($this->items) === count(array_filter($this->items))) {
                // Remove by index
                unset($this->items[(int)$itemIdentifier]);
            }
        }
        
        $this->items = array_values($this->items); // Reindex array
        
        // Always keep at least one item
        if (empty($this->items)) {
            $this->addItem();
        }
        
        $this->calculateTotals();
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
        // Recalculate amounts for all items
        foreach ($this->items as $index => $item) {
            $this->calculateItemAmount($index);
        }
        $this->calculateTotals();
    }

    public function updatedBilledToId(): void
    {
        // Update all items client_id when main client changes
        foreach ($this->items as $index => $item) {
            $this->items[$index]['client_id'] = $this->billed_to_id;
        }
    }

    public function updatedDiscountValue(): void
    {
        $this->calculateTotals();
    }

    public function updatedDiscountType(): void
    {
        $this->discount_value = null;
        $this->calculateTotals();
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
        // Calculate subtotal
        $this->subtotal = array_sum(array_column($this->items, 'amount'));
        
        // Calculate discount
        if ($this->discount_value > 0) {
            if ($this->discount_type === 'percentage') {
                // Convert percentage to amount
                $this->discount_amount = (int) (($this->subtotal * $this->discount_value) / 100);
            } else {
                // Fixed amount
                $this->discount_amount = (int) $this->discount_value;
            }
            
            // Ensure discount doesn't exceed subtotal
            $this->discount_amount = min($this->discount_amount, $this->subtotal);
        } else {
            $this->discount_amount = 0;
        }
        
        // Calculate total
        $this->total_amount = $this->subtotal - $this->discount_amount;
    }

    public function save(): void
    {
        $this->validate();

        try {
            // Create invoice
            $invoice = Invoice::create([
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
                'status' => 'draft',
            ]);

            // Create invoice items
            foreach ($this->items as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'client_id' => $item['client_id'],
                    'service_name' => $item['service_name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => (int) $item['unit_price'],
                    'amount' => $item['amount'],
                ]);
            }

            // Store invoice number for success message
            $invoiceNumber = $this->invoice_number;
            
            // Reset form and close modal
            $this->resetData();
            
            // Show success message
            $this->toast()->success('Berhasil', "Invoice {$invoiceNumber} berhasil dibuat")->send();
            
            // Dispatch events
            $this->dispatch('invoice-created');
            $this->dispatch('invoice-updated');

        } catch (\Exception $e) {
            $this->toast()->error('Error', 'Gagal membuat invoice: ' . $e->getMessage())->send();
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
        return view('livewire.invoices.create');
    }
}