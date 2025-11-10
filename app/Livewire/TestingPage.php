<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\Service;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class TestingPage extends Component
{
    // Invoice data
    public $invoice = [
        'client_id' => null,
        'issue_date' => null,
        'due_date' => null,
    ];

    // Items untuk save
    public $items = [];

    public function save()
    {
        // Validasi
        $this->validate([
            'invoice.client_id' => 'required|exists:clients,id',
            'invoice.issue_date' => 'required|date',
            'invoice.due_date' => 'required|date|after_or_equal:invoice.issue_date',
            'items' => 'required|array|min:1',
            'items.*.client_id' => 'required|exists:clients,id',
            'items.*.service_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required',
            'items.*.cogs_amount' => 'nullable',
            'items.*.is_tax_deposit' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // Parse items dan hitung totals
            $parsedItems = [];
            $subtotal = 0;

            foreach ($this->items as $item) {
                $unitPrice = $this->parseAmount($item['unit_price']);
                $quantity = $item['quantity'];
                $amount = $unitPrice * $quantity;
                $cogsAmount = $this->parseAmount($item['cogs_amount'] ?? '0');
                $isTaxDeposit = $item['is_tax_deposit'] ?? false;

                $parsedItems[] = [
                    'client_id' => $item['client_id'],
                    'service_name' => $item['service_name'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'amount' => $amount,
                    'cogs_amount' => $cogsAmount,
                    'is_tax_deposit' => $isTaxDeposit,
                ];

                $subtotal += $amount;
            }

            // Generate invoice number
            $invoiceNumber = $this->generateInvoiceNumber();

            // Create Invoice
            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'billed_to_id' => $this->invoice['client_id'],
                'subtotal' => $subtotal,
                'discount_amount' => 0,
                'discount_type' => 'fixed',
                'discount_value' => 0,
                'discount_reason' => null,
                'total_amount' => $subtotal,
                'issue_date' => $this->invoice['issue_date'],
                'due_date' => $this->invoice['due_date'],
                'status' => 'draft',
            ]);

            // Create Invoice Items
            foreach ($parsedItems as $itemData) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'client_id' => $itemData['client_id'],
                    'service_name' => $itemData['service_name'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'amount' => $itemData['amount'],
                    'cogs_amount' => $itemData['cogs_amount'],
                    'is_tax_deposit' => $itemData['is_tax_deposit'],
                ]);
            }

            DB::commit();

            // Success notification
            session()->flash('success', "Invoice {$invoiceNumber} has been created successfully!");

            // Reset form
            $this->reset(['invoice', 'items']);

            // Refresh page
            return $this->redirect(request()->header('Referer'), navigate: true);

        } catch (\Exception $e) {
            DB::rollBack();

            // Log error
            \Log::error('Failed to create invoice: ' . $e->getMessage(), [
                'exception' => $e,
                'invoice' => $this->invoice,
                'items' => $this->items
            ]);

            // Error notification
            session()->flash('error', 'Failed to create invoice. Please try again.');
        }
    }

    private function generateInvoiceNumber(): string
    {
        $yearMonth = date('Ym');

        $lastInvoice = Invoice::where('invoice_number', 'like', "INV-{$yearMonth}-%")
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('INV-%s-%04d', $yearMonth, $newNumber);
    }

    private function parseAmount($value): int
    {
        if (empty($value))
            return 0;
        return (int) preg_replace('/[^0-9]/', '', $value);
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
        return view('livewire.testing-page');
    }
}