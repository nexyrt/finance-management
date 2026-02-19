<?php

namespace App\Livewire\Invoices;

use App\Models\Invoice;
use Livewire\Component;
use Livewire\Attributes\On;
use TallStackUi\Traits\Interactions;
use Illuminate\Support\Facades\DB;

class Delete extends Component
{
    use Interactions;

    public ?Invoice $invoice = null;

    public function render(): string
    {
        return <<<'HTML'
        <div></div>
        HTML;
    }

    #[On('delete-invoice')]
    public function confirm(int $invoiceId): void
    {
        $this->invoice = Invoice::with(['client', 'items', 'payments'])->find($invoiceId);

        if (!$this->invoice) {
            $this->toast()->error(__('common.error'), __('invoice.not_found'))->send();
            return;
        }

        // Build confirmation message
        $title = __('invoice.delete_confirm_title') . ' ' . $this->invoice->invoice_number . '?';
        $description = $this->buildConfirmationMessage();

        $this->dialog()
            ->question($title, $description)
            ->confirm(__('invoice.confirm_delete'), 'delete', __('invoice.delete_success'))
            ->cancel(__('common.cancel'))
            ->send();
    }

    public function delete(): void
    {
        if (!$this->invoice) {
            $this->toast()->error(__('common.error'), __('invoice.not_found'))->send();
            return;
        }

        try {
            // Capture data before deletion
            $invoiceNumber = $this->invoice->invoice_number;
            $clientName    = $this->invoice->client->name;
            $deletedBy     = auth()->user()->name;

            DB::transaction(function () {
                // Delete payments first
                if ($this->invoice->payments->count() > 0) {
                    $this->invoice->payments()->delete();
                }

                // Delete invoice items
                $this->invoice->items()->delete();

                // Delete the invoice
                $this->invoice->delete();
            });

            // Notify admins & finance managers about deletion
            $recipients = \App\Models\User::role(['admin', 'finance manager'])->pluck('id')->toArray();
            \App\Models\AppNotification::notifyMany(
                $recipients,
                'invoice_deleted',
                'Invoice Dihapus',
                'Invoice ' . $invoiceNumber . ' (' . $clientName . ') telah dihapus oleh ' . $deletedBy,
                ['url' => route('invoices.index')]
            );

            // Success notification
            $this->toast()
                ->success(__('common.success'), __('invoice.deletion_success'))
                ->send();

            // Dispatch refresh events
            $this->dispatch('invoice-updated');
            $this->dispatch('invoice-deleted');

        } catch (\Exception $e) {
            $this->toast()
                ->error(__('common.error'), __('invoice.delete_error') . ': ' . $e->getMessage())
                ->send();
        }

        // Reset
        $this->invoice = null;
    }

    private function buildConfirmationMessage(): string
    {
        $clientName = $this->invoice->client->name;
        $totalAmount = number_format($this->invoice->total_amount, 0, ',', '.');
        $itemsCount = $this->invoice->items->count();
        $paymentsCount = $this->invoice->payments->count();

        $message = __('invoice.delete_confirm_message', [
            'client_name' => $clientName,
            'total_amount' => $totalAmount,
            'items_count' => $itemsCount
        ]);

        if ($paymentsCount > 0) {
            $totalPaid = number_format($this->invoice->amount_paid, 0, ',', '.');
            $message .= ' ' . __('invoice.delete_confirm_with_payments', [
                'payments_count' => $paymentsCount,
                'total_paid' => $totalPaid
            ]);
        }

        $message .= ' ' . __('invoice.delete_permanent_note');

        return $message;
    }
}