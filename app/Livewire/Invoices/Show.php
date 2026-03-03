<?php

namespace App\Livewire\Invoices;

use App\Models\Invoice;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use TallStackUi\Traits\Interactions;

class Show extends Component
{
    use Interactions;

    public ?int $invoiceId = null;
    public bool $modal = false;

    #[On('show-invoice')]
    public function show(int $invoiceId): void
    {
        $invoice = Invoice::find($invoiceId);

        if (!$invoice) {
            $this->toast()->error(__('common.error'), __('invoice.not_found'))->send();
            return;
        }

        $this->invoiceId = $invoiceId;
        $this->modal = true;
    }

    public function resetData(): void
    {
        $this->invoiceId = null;
        $this->modal = false;
    }

    #[Computed]
    public function invoice(): ?Invoice
    {
        return $this->invoiceId
            ? Invoice::with(['client', 'items.client', 'payments.bankAccount'])->find($this->invoiceId)
            : null;
    }

    #[Computed]
    public function invoiceMetrics(): array
    {
        if (!$this->invoice) {
            return ['netRevenue' => 0, 'totalCogs' => 0, 'totalTaxDeposits' => 0, 'grossProfit' => 0];
        }

        $items = $this->invoice->items;
        $regular = $items->where('is_tax_deposit', false);
        $tax = $items->where('is_tax_deposit', true);

        $netRevenue = $regular->sum('amount');
        $totalCogs = $regular->sum('cogs_amount');
        $totalTaxDeposits = $tax->sum('amount');
        $grossProfit = $this->invoice->total_amount - $totalTaxDeposits - $totalCogs;

        return compact('netRevenue', 'totalCogs', 'totalTaxDeposits', 'grossProfit');
    }

    #[Computed]
    public function netRevenue(): int
    {
        return $this->invoiceMetrics['netRevenue'];
    }

    #[Computed]
    public function totalCogs(): int
    {
        return $this->invoiceMetrics['totalCogs'];
    }

    #[Computed]
    public function totalTaxDeposits(): int
    {
        return $this->invoiceMetrics['totalTaxDeposits'];
    }

    #[Computed]
    public function grossProfit(): int
    {
        return $this->invoiceMetrics['grossProfit'];
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

    public function render(): View
    {
        return view('livewire.invoices.show', [
            'invoice' => $this->invoice,
        ]);
    }
}
