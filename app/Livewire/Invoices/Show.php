<?php

namespace App\Livewire\Invoices;

use App\Models\Invoice;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Show extends Component
{
    use Interactions;

    public ?int $invoiceId = null;

    public bool $modal = false;

    public bool $sendModal = false;

    public string $pendingInvoiceNumber = '';

    #[On('show-invoice')]
    public function show(int $invoiceId): void
    {
        $invoice = Invoice::find($invoiceId);

        if (! $invoice) {
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
        if (! $this->invoice) {
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

    public function prepareSendInvoice(): void
    {
        if (! $this->invoice || $this->invoice->status !== 'draft') {
            return;
        }

        $issueDate = \Carbon\Carbon::parse($this->invoice->issue_date);
        $this->pendingInvoiceNumber = $this->invoice->invoice_number
            ?? Invoice::generateInvoiceNumber($issueDate, $this->invoice->billed_to_id);
        $this->sendModal = true;
    }

    public function confirmSendInvoice(): void
    {
        $this->validate([
            'pendingInvoiceNumber' => 'required|string|max:100|unique:invoices,invoice_number,'.$this->invoiceId,
        ], [
            'pendingInvoiceNumber.required' => __('invoice.invoice_number_required'),
            'pendingInvoiceNumber.unique' => __('invoice.invoice_number_already_used'),
        ]);

        if (! $this->invoice || $this->invoice->status !== 'draft') {
            return;
        }

        $this->invoice->update([
            'invoice_number' => $this->pendingInvoiceNumber,
            'status' => 'sent',
        ]);

        unset($this->invoice);

        $this->sendModal = false;
        $this->toast()->success(__('common.success'), __('invoice.send_success', ['invoice_number' => $this->pendingInvoiceNumber]))->send();
        $this->dispatch('invoice-updated');
    }

    public function recordPayment(): void
    {
        if (! $this->invoice) {
            return;
        }
        $this->dispatch('record-payment', invoiceId: $this->invoice->id);
    }

    public function editInvoice(): void
    {
        if (! $this->invoice) {
            return;
        }
        $this->resetData();
        $this->redirect(route('invoices.edit', $this->invoice->id), navigate: true);
    }

    public function showPaymentAttachment(int $paymentId): void
    {
        $this->dispatch('show-payment-attachment', paymentId: $paymentId);
    }

    public function printInvoice(): void
    {
        if (! $this->invoice) {
            return;
        }

        $this->dispatch('print-invoice', [
            'invoiceId' => $this->invoice->id,
            'invoiceNumber' => $this->invoice->invoice_number,
        ]);
    }

    public function render(): View
    {
        return view('livewire.invoices.show', [
            'invoice' => $this->invoice,
        ]);
    }
}
