<?php

namespace App\Livewire\Payments;

use App\Models\Payment;
use App\Models\Invoice;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use TallStackUi\Traits\Interactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\View\View;

class Delete extends Component
{
    use Interactions;

    public ?Payment $payment = null;
    public ?Invoice $invoice = null;
    public bool $modal = false;

    // Predicted status info
    public string $predictedStatus = '';
    public int $remainingPaid = 0;
    public string $statusText = '';
    public string $statusColor = '';

    public function render(): View
    {
        return view('livewire.payments.delete');
    }

    #[On('delete-payment')]
    public function load(int $paymentId): void
    {
        $this->payment = Payment::with(['invoice.client', 'bankAccount'])->find($paymentId);

        if (!$this->payment) {
            $this->toast()->error('Error', 'Payment tidak ditemukan')->send();
            return;
        }

        $this->invoice = $this->payment->invoice;

        // Calculate predicted status
        $this->calculatePredictedStatus();

        $this->modal = true;
    }

    public function delete(): void
    {
        if (!$this->payment) {
            $this->toast()->error('Error', 'Payment tidak ditemukan')->send();
            return;
        }

        try {
            DB::transaction(function () {
                $invoice = $this->payment->invoice;
                $oldStatus = $invoice->status;

                // Delete attachment if exists
                if ($this->payment->attachment_path && Storage::exists($this->payment->attachment_path)) {
                    Storage::delete($this->payment->attachment_path);
                }

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

            // Close modal and reset
            $this->modal = false;
            $this->reset(['payment', 'invoice', 'predictedStatus', 'remainingPaid', 'statusText', 'statusColor']);

        } catch (\Exception $e) {
            $this->toast()->error('Error', 'Gagal menghapus payment: ' . $e->getMessage())->send();
        }
    }

    private function calculatePredictedStatus(): void
    {
        if (!$this->payment || !$this->invoice) {
            return;
        }

        // Calculate remaining payments after deletion
        $this->remainingPaid = $this->invoice->payments()
            ->where('id', '!=', $this->payment->id)
            ->sum('amount');

        $totalAmount = $this->invoice->total_amount;
        $dueDate = $this->invoice->due_date;

        $this->predictedStatus = $this->predictNewStatus(
            $this->remainingPaid,
            $totalAmount,
            $dueDate
        );

        // Set status text and color
        $this->statusText = $this->getStatusText($this->predictedStatus);
        $this->statusColor = $this->getStatusColor($this->predictedStatus);
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
        return match ($status) {
            'paid' => 'Lunas',
            'partially_paid' => 'Sebagian Dibayar',
            'sent' => 'Terkirim',
            'overdue' => 'Terlambat',
            'draft' => 'Draft',
            default => ucfirst($status)
        };
    }

    private function getStatusColor(string $status): string
    {
        return match ($status) {
            'paid' => 'green',
            'partially_paid' => 'yellow',
            'sent' => 'blue',
            'overdue' => 'red',
            'draft' => 'gray',
            default => 'gray'
        };
    }

    private function logStatusChange(Invoice $invoice, string $oldStatus, string $newStatus): void
    {
        // Log status change untuk audit trail
        \Log::info("Invoice {$invoice->invoice_number} status changed from {$oldStatus} to {$newStatus} due to payment deletion");
    }
}