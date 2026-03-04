<?php

namespace App\Livewire\Invoices;

use App\Models\AppNotification;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Delete extends Component
{
    use Interactions;

    public ?Invoice $invoice = null;

    public bool $modal = false;

    public string $action = 'cancel'; // 'cancel' | 'permanent'

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.invoices.delete');
    }

    #[On('delete-invoice')]
    public function confirm(int $invoiceId): void
    {
        $this->invoice = Invoice::with(['client', 'items', 'payments'])->find($invoiceId);

        if (! $this->invoice) {
            $this->toast()->error(__('common.error'), __('invoice.not_found'))->send();

            return;
        }

        $this->action = 'cancel';
        $this->modal = true;
    }

    public function cancel(): void
    {
        if (! $this->invoice) {
            $this->toast()->error(__('common.error'), __('invoice.not_found'))->send();

            return;
        }

        if ($this->invoice->status === 'cancelled') {
            $this->toast()->warning(__('common.warning'), __('invoice.already_cancelled'))->send();
            $this->modal = false;

            return;
        }

        try {
            $invoiceNumber = $this->invoice->invoice_number;

            $this->invoice->update(['status' => 'cancelled']);

            $recipients = User::role(['admin', 'finance manager'])->pluck('id')->toArray();
            AppNotification::notifyMany(
                $recipients,
                'invoice_cancelled',
                'Invoice Dibatalkan',
                'Invoice '.$invoiceNumber.' ('.$this->invoice->client->name.') telah dibatalkan oleh '.auth()->user()->name,
                ['url' => route('invoices.index')]
            );

            $this->toast()
                ->success(__('common.success'), __('invoice.cancel_invoice_success', ['number' => $invoiceNumber]))
                ->send();

            $this->dispatch('invoice-updated');
            $this->dispatch('invoice-deleted');

        } catch (\Exception $e) {
            $this->toast()
                ->error(__('common.error'), __('invoice.cancel_invoice_error').': '.$e->getMessage())
                ->send();
        }

        $this->modal = false;
        $this->invoice = null;
    }

    public function delete(): void
    {
        if (! $this->invoice) {
            $this->toast()->error(__('common.error'), __('invoice.not_found'))->send();

            return;
        }

        try {
            $invoiceNumber = $this->invoice->invoice_number;
            $clientName = $this->invoice->client->name;
            $deletedBy = auth()->user()->name;

            DB::transaction(function () {
                if ($this->invoice->payments->count() > 0) {
                    $this->invoice->payments()->delete();
                }

                $this->invoice->items()->delete();
                $this->invoice->delete();
            });

            $recipients = User::role(['admin', 'finance manager'])->pluck('id')->toArray();
            AppNotification::notifyMany(
                $recipients,
                'invoice_deleted',
                'Invoice Dihapus',
                'Invoice '.$invoiceNumber.' ('.$clientName.') telah dihapus oleh '.$deletedBy,
                ['url' => route('invoices.index')]
            );

            $this->toast()
                ->success(__('common.success'), __('invoice.deletion_success'))
                ->send();

            $this->dispatch('invoice-updated');
            $this->dispatch('invoice-deleted');

        } catch (\Exception $e) {
            $this->toast()
                ->error(__('common.error'), __('invoice.delete_error').': '.$e->getMessage())
                ->send();
        }

        $this->modal = false;
        $this->invoice = null;
    }
}
