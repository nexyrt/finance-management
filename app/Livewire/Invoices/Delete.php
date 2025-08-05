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

    #[On('delete-invoice')]
    public function showDeleteDialog(int $invoiceId): void
    {
        $this->invoice = Invoice::with(['client', 'items', 'payments.bankAccount'])->find($invoiceId);

        if (!$this->invoice) {
            $this->toast()->error('Error', 'Invoice tidak ditemukan')->send();
            return;
        }

        // Show confirmation dialog
        $this->dialog()
            ->question('Konfirmasi Hapus Invoice', $this->getConfirmationMessage())
            ->confirm('Ya, Hapus Invoice', 'confirmDelete', 'Invoice berhasil dihapus')
            ->cancel('Batal')
            ->send();
    }

    public function confirmDelete(): void
    {
        if (!$this->invoice) {
            $this->toast()->error('Error', 'Invoice tidak ditemukan')->send();
            return;
        }

        try {
            DB::transaction(function () {
                // Delete related payments first
                if ($this->invoice->payments->count() > 0) {
                    $this->invoice->payments()->delete();
                }

                // Delete invoice items
                $this->invoice->items()->delete();

                // Delete the invoice
                $this->invoice->delete();
            });

            // Success feedback
            $this->toast()->success('Berhasil', 'Invoice berhasil dihapus')->persistent()->send();

            // Dispatch events to refresh other components
            $this->dispatch('invoice-deleted');
            $this->dispatch('invoice-updated');

        } catch (\Exception $e) {
            $this->toast()->error('Error', 'Gagal menghapus invoice: ' . $e->getMessage())->send();
        }

        // Reset data
        $this->reset(['invoice']);
    }

    private function getConfirmationMessage(): string
    {
        if (!$this->invoice) {
            return 'Apakah Anda yakin ingin menghapus invoice ini?';
        }

        $invoiceNumber = $this->invoice->invoice_number;
        $clientName = $this->invoice->client->name;
        $totalAmount = number_format($this->invoice->total_amount, 0, ',', '.');
        $itemsCount = $this->invoice->items->count();
        $paymentsCount = $this->invoice->payments->count();
        $totalPaid = $this->invoice->amount_paid;

        $message = "Anda akan menghapus invoice {$invoiceNumber} untuk {$clientName}.\n\n";
        $message .= "Detail yang akan dihapus:\n";
        $message .= "• Total invoice: Rp {$totalAmount}\n";
        $message .= "• {$itemsCount} item invoice\n";
        
        if ($paymentsCount > 0) {
            $paidAmount = number_format($totalPaid, 0, ',', '.');
            $message .= "• {$paymentsCount} pembayaran (Rp {$paidAmount})\n\n";
            $message .= "⚠️ PERHATIAN: Semua pembayaran terkait juga akan dihapus!";
        } else {
            $message .= "• Belum ada pembayaran";
        }

        return $message;
    }

    public function render()
    {
        return view('livewire.invoices.delete');
    }
}