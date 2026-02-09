<?php

namespace App\Livewire\Invoices;

use App\Models\Invoice;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use TallStackUi\Traits\Interactions;

class Show extends Component
{
    use Interactions;

    public ?Invoice $invoice = null;
    public bool $modal = false;
    public string $selectedTab = 'overview';

    // Get translated tab name for display
    #[Computed]
    public function translatedTab(): string
    {
        return match($this->selectedTab) {
            'overview' => __('pages.overview'),
            'payments' => __('pages.payments_tab'),
            'details' => __('pages.details'),
            default => ucfirst($this->selectedTab),
        };
    }

    #[On('show-invoice')]
    public function show(int $invoiceId, string $tab = 'payments'): void
    {
        $this->invoice = Invoice::with(['client', 'items.client', 'payments.bankAccount'])
            ->find($invoiceId);

        if (!$this->invoice) {
            $this->toast()->error(__('common.error'), __('invoice.not_found'))->send();
            return;
        }

        $this->selectedTab = $tab;
        $this->modal = true;
    }

    public function resetData(): void
    {
        $this->invoice = null;
        $this->modal = false;
        $this->selectedTab = 'overview';
    }

    #[Computed]
    public function netRevenue(): int
    {
        if (!$this->invoice)
            return 0;
        return $this->invoice->items->where('is_tax_deposit', false)->sum('amount');
    }

    #[Computed]
    public function totalCogs(): int
    {
        if (!$this->invoice)
            return 0;
        return $this->invoice->items->where('is_tax_deposit', false)->sum('cogs_amount');
    }

    #[Computed]
    public function totalTaxDeposits(): int
    {
        if (!$this->invoice)
            return 0;
        return $this->invoice->items->where('is_tax_deposit', true)->sum('amount');
    }

    #[Computed]
    public function grossProfit(): int
    {
        if (!$this->invoice)
            return 0;
        return $this->invoice->total_amount - $this->totalTaxDeposits - $this->totalCogs;
    }

    public function sendInvoice(): void
    {
        if (!$this->invoice || $this->invoice->status !== 'draft')
            return;

        $this->invoice->update(['status' => 'sent']);
        $this->toast()->success(__('common.success'), __('invoice.send_success', ['invoice_number' => $this->invoice->invoice_number]))->send();
        $this->dispatch('invoice-updated');
    }

    public function recordPayment(): void
    {
        if (!$this->invoice)
            return;
        $this->dispatch('record-payment', invoiceId: $this->invoice->id);
    }

    public function editInvoice(): void
    {
        if (!$this->invoice)
            return;
        $this->resetData();
        $this->redirect(route('invoices.edit', $this->invoice->id), navigate: true);
    }

    public function showPaymentAttachment(int $paymentId): void
    {
        $this->dispatch('show-payment-attachment', paymentId: $paymentId);
    }

    public function printInvoice(): void
    {
        if (!$this->invoice)
            return;

        $this->dispatch('print-invoice', [
            'invoiceId' => $this->invoice->id,
            'invoiceNumber' => $this->invoice->invoice_number
        ]);
    }

    public function render()
    {
        return view('livewire.invoices.show');
    }
}