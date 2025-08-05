<?php

namespace App\Livewire\Payments;

use App\Models\Payment;
use App\Models\BankAccount;
use Livewire\Component;
use Livewire\Attributes\On;
use TallStackUi\Traits\Interactions;
use Illuminate\Support\Facades\DB;

class Edit extends Component
{
    use Interactions;

    public ?Payment $payment = null;
    public bool $showModal = false;

    // Form properties
    public $amount = null;
    public string $payment_date = '';
    public string $payment_method = 'bank_transfer';
    public string $bank_account_id = '';
    public string $reference_number = '';

    protected array $rules = [
        'amount' => 'required|numeric|min:1',
        'payment_date' => 'required|date',
        'payment_method' => 'required|in:cash,bank_transfer',
        'bank_account_id' => 'required|exists:bank_accounts,id',
        'reference_number' => 'nullable|string|max:255',
    ];

    #[On('edit-payment')]
    public function editPayment(int $paymentId): void
    {
        $this->payment = Payment::with(['invoice.client', 'bankAccount'])->find($paymentId);
        
        if (!$this->payment) {
            $this->toast()->error('Error', 'Payment tidak ditemukan')->send();
            return;
        }

        // Fill form with existing data
        $this->amount = $this->payment->amount;
        $this->payment_date = $this->payment->payment_date->format('Y-m-d');
        $this->payment_method = $this->payment->payment_method;
        $this->bank_account_id = $this->payment->bank_account_id;
        $this->reference_number = $this->payment->reference_number ?? '';
        
        $this->showModal = true;
    }

    public function resetData(): void
    {
        $this->payment = null;
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->amount = null;
        $this->payment_date = '';
        $this->payment_method = 'bank_transfer';
        $this->bank_account_id = '';
        $this->reference_number = '';
        $this->resetValidation();
    }

    public function save(): void
    {
        if (!$this->payment) {
            $this->toast()->error('Error', 'Payment tidak ditemukan')->send();
            return;
        }

        $this->validate();

        try {
            DB::transaction(function () {
                $oldStatus = $this->payment->invoice->status;
                
                // Amount sudah dalam format numeric
                $amountInteger = (int) $this->amount;
                
                // Validate payment amount doesn't exceed invoice total
                $invoice = $this->payment->invoice;
                $otherPayments = $invoice->payments()->where('id', '!=', $this->payment->id)->sum('amount');
                $maxAllowed = $invoice->total_amount - $otherPayments;
                
                if ($amountInteger > $maxAllowed) {
                    $this->addError('amount', 'Jumlah pembayaran tidak boleh melebihi sisa tagihan: Rp ' . number_format($maxAllowed, 0, ',', '.'));
                    return;
                }

                // Update payment
                $this->payment->update([
                    'amount' => $amountInteger,
                    'payment_date' => $this->payment_date,
                    'payment_method' => $this->payment_method,
                    'bank_account_id' => $this->bank_account_id,
                    'reference_number' => $this->reference_number ?: null,
                ]);

                // Recalculate invoice status
                $invoice->refresh();
                $newStatus = $this->evaluateInvoiceStatus($invoice);
                $invoice->update(['status' => $newStatus]);

                // Log status change if different
                if ($oldStatus !== $newStatus) {
                    $this->logStatusChange($invoice, $oldStatus, $newStatus);
                }
            });

            $this->toast()->success('Berhasil', 'Pembayaran berhasil diperbarui')->send();
            $this->resetData();
            
            // Dispatch events untuk refresh components
            $this->dispatch('payment-updated');
            $this->dispatch('invoice-updated');

        } catch (\Exception $e) {
            $this->toast()->error('Error', 'Gagal memperbarui pembayaran: ' . $e->getMessage())->send();
        }
    }

    private function evaluateInvoiceStatus($invoice): string
    {
        $totalPaid = $invoice->payments()->sum('amount');
        $totalAmount = $invoice->total_amount;
        $dueDate = $invoice->due_date;

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

    private function logStatusChange($invoice, string $oldStatus, string $newStatus): void
    {
        \Log::info("Invoice {$invoice->invoice_number} status changed from {$oldStatus} to {$newStatus} due to payment edit");
    }

    public function getBankAccountsProperty()
    {
        return BankAccount::select('id', 'account_name', 'bank_name', 'account_number')
            ->orderBy('bank_name')
            ->get()
            ->map(fn($account) => [
                'label' => "{$account->bank_name} - {$account->account_name} ({$account->account_number})",
                'value' => $account->id
            ]);
    }

    public function render()
    {
        return view('livewire.payments.edit');
    }
}