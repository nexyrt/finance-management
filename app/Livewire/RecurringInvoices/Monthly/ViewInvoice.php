<?php

namespace App\Livewire\RecurringInvoices\Monthly;

use App\Models\RecurringInvoice;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;

class ViewInvoice extends Component
{
    public ?RecurringInvoice $invoice = null;
    public bool $modal = false;

    #[On('view-invoice')]
    public function load($invoiceId): void
    {
        $this->invoice = RecurringInvoice::with(['client', 'template', 'publishedInvoice'])->find($invoiceId);

        if (!$this->invoice) {
            return;
        }

        $this->modal = true;
    }

    #[Computed]
    public function items(): array
    {
        return $this->invoice->invoice_data['items'] ?? [];
    }

    #[Computed]
    public function subtotal(): int
    {
        return $this->invoice->invoice_data['subtotal'] ?? 0;
    }

    #[Computed]
    public function discountAmount(): int
    {
        return $this->invoice->invoice_data['discount_amount'] ?? 0;
    }

    #[Computed]
    public function totalAmount(): int
    {
        return $this->invoice->invoice_data['total_amount'] ?? 0;
    }

    #[Computed]
    public function totalCogs(): int
    {
        return collect($this->items)->sum('cogs_amount');
    }

    #[Computed]
    public function grossProfit(): int
    {
        return $this->totalAmount - $this->totalCogs;
    }

    #[Computed]
    public function grossProfitMargin(): float
    {
        if ($this->totalAmount == 0)
            return 0;
        return ($this->grossProfit / $this->totalAmount) * 100;
    }

    public function render()
    {
        return view('livewire.recurring-invoices.monthly.view-invoice');
    }
}