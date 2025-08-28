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
            $this->toast()->error('Error', 'Invoice tidak ditemukan')->send();
            return;
        }

        // Build confirmation message
        $title = "Hapus Invoice {$this->invoice->invoice_number}?";
        $description = $this->buildConfirmationMessage();

        $this->dialog()
            ->question($title, $description)
            ->confirm('Ya, Hapus Invoice', 'delete', 'Invoice berhasil dihapus')
            ->cancel('Batal')
            ->send();
    }

    public function delete(): void
    {
        if (!$this->invoice) {
            $this->toast()->error('Error', 'Invoice tidak ditemukan')->send();
            return;
        }

        try {
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

            // Success notification
            $this->toast()
                ->success('Berhasil', 'Invoice berhasil dihapus')
                ->send();

            // Dispatch refresh events
            $this->dispatch('invoice-updated');
            $this->dispatch('invoice-deleted');

        } catch (\Exception $e) {
            $this->toast()
                ->error('Error', 'Gagal menghapus invoice: ' . $e->getMessage())
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
        
        $message = "Invoice untuk {$clientName} senilai Rp {$totalAmount} dengan {$itemsCount} item";
        
        if ($paymentsCount > 0) {
            $totalPaid = number_format($this->invoice->amount_paid, 0, ',', '.');
            $message .= " dan {$paymentsCount} pembayaran (Rp {$totalPaid})";
        }
        
        $message .= " akan dihapus permanen.";
        
        return $message;
    }
}