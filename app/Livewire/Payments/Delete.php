<?php

namespace App\Livewire\Payments;

use App\Models\Payment;
use App\Models\Invoice;
use Livewire\Component;
use Livewire\Attributes\On;
use TallStackUi\Traits\Interactions;
use Illuminate\Support\Facades\DB;

class Delete extends Component
{
    use Interactions;

    public ?Payment $payment = null;
    public ?Invoice $invoice = null;

    #[On('delete-payment')]
    public function showDeleteDialog(int $paymentId): void
    {
        $this->payment = Payment::with(['invoice.client', 'bankAccount'])->find($paymentId);

        if (!$this->payment) {
            $this->toast()->error('Error', 'Payment tidak ditemukan')->send();
            return;
        }

        $this->invoice = $this->payment->invoice;

        // Show confirmation dialog with higher z-index
        $this->dialog()
            ->question('Konfirmasi Hapus Payment', $this->getConfirmationMessage())
            ->confirm('Ya, Hapus Payment', 'confirmDelete', 'Payment berhasil dihapus')
            ->cancel('Batal')
            ->send();
    }

    public function confirmDelete(): void
    {
        if (!$this->payment) {
            $this->toast()->error('Error', 'Payment tidak ditemukan')->send();
            return;
        }

        try {
            DB::transaction(function () {
                $invoice = $this->payment->invoice;
                $oldStatus = $invoice->status;

                // Delete payment
                $this->payment->delete();

                // Recalculate invoice status
                $newStatus = $this->evaluateInvoiceStatus($invoice);
                $invoice->update(['status' => $newStatus]);

                // Log status change if different
                if ($oldStatus !== $newStatus) {
                    $this->logStatusChange($invoice, $oldStatus, $newStatus);
                }
            });

            // Success feedback
            $this->toast()->success('Berhasil', 'Payment berhasil dihapus')->send();
            
            // Dispatch events to refresh other components
            $this->dispatch('payment-deleted');
            $this->dispatch('invoice-updated');

        } catch (\Exception $e) {
            $this->toast()->error('Error', 'Gagal menghapus payment: ' . $e->getMessage())->send();
        }

        // Reset data
        $this->reset(['payment', 'invoice']);
    }

    private function getConfirmationMessage(): string
    {
        if (!$this->payment || !$this->invoice) {
            return 'Apakah Anda yakin ingin menghapus payment ini?';
        }

        $paymentAmount = number_format($this->payment->amount, 0, ',', '.');
        $invoiceNumber = $this->invoice->invoice_number;
        $clientName = $this->invoice->client->name;
        
        // Calculate remaining payments after deletion
        $totalPaid = $this->invoice->payments()->where('id', '!=', $this->payment->id)->sum('amount');
        $totalAmount = $this->invoice->total_amount;
        
        // Predict new status
        $newStatus = $this->predictNewStatus($totalPaid, $totalAmount, $this->invoice->due_date);
        $statusText = $this->getStatusText($newStatus);

        return "Anda akan menghapus payment sebesar Rp {$paymentAmount} untuk invoice {$invoiceNumber} ({$clientName}).\n\n" .
               "Status invoice akan berubah menjadi: {$statusText}";
    }

    private function evaluateInvoiceStatus(Invoice $invoice): string
    {
        $invoice->refresh();
        $totalPaid = $invoice->payments()->sum('amount');
        $totalAmount = $invoice->total_amount;
        $dueDate = $invoice->due_date;

        return $this->predictNewStatus($totalPaid, $totalAmount, $dueDate);
    }

    private function predictNewStatus(int $totalPaid, int $totalAmount, $dueDate): string
    {
        // Paid (including overpaid)
        if ($totalPaid >= $totalAmount && $totalPaid > 0) {
            return 'paid';
        }

        // Partially paid
        if ($totalPaid > 0 && $totalPaid < $totalAmount) {
            return 'partially_paid';
        }

        // No payment yet
        if ($totalPaid == 0) {
            return $dueDate->isPast() ? 'overdue' : 'sent';
        }

        return 'sent'; // Fallback
    }

    private function getStatusText(string $status): string
    {
        return match($status) {
            'paid' => 'Lunas',
            'partially_paid' => 'Sebagian Dibayar',
            'sent' => 'Terkirim',
            'overdue' => 'Terlambat',
            'draft' => 'Draft',
            default => ucfirst($status)
        };
    }

    private function logStatusChange(Invoice $invoice, string $oldStatus, string $newStatus): void
    {
        // Log status change untuk audit trail
        \Log::info("Invoice {$invoice->invoice_number} status changed from {$oldStatus} to {$newStatus} due to payment deletion");
    }

    public function render()
    {
        return view('livewire.payments.delete');
    }
}