<?php

namespace App\Livewire\Payments;

use App\Models\Payment;
use App\Models\BankAccount;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use TallStackUi\Traits\Interactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Edit extends Component
{
    use Interactions, WithFileUploads;

    public ?Payment $payment = null;
    public bool $showModal = false;

    // Form properties
    public $amount = null;
    public string $payment_date = '';
    public string $payment_method = 'bank_transfer';
    public string $bank_account_id = '';
    public string $reference_number = '';
    public $attachment = null;

    protected array $rules = [
        'amount' => 'required|numeric|min:1',
        'payment_date' => 'required|date',
        'payment_method' => 'required|in:cash,bank_transfer',
        'bank_account_id' => 'required|exists:bank_accounts,id',
        'reference_number' => 'nullable|string|max:255',
        'attachment' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf'
    ];

    #[On('edit-payment')]
    public function editPayment(int $paymentId): void
    {
        $this->payment = Payment::with(['invoice.client', 'bankAccount'])->find($paymentId);

        if (!$this->payment) {
            $this->toast()->error(__('common.error'), __('pages.payment_not_found'))->send();
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
        $this->attachment = null;
        $this->resetValidation();
    }

    public function save(): void
    {
        if (!$this->payment) {
            $this->toast()->error(__('common.error'), __('pages.payment_not_found'))->send();
            return;
        }

        $this->validate();

        try {
            DB::transaction(function () {
                $oldStatus = $this->payment->invoice->status;

                $amountInteger = (int) $this->amount;

                // Validate payment amount doesn't exceed invoice total
                $invoice = $this->payment->invoice;
                $otherPayments = $invoice->payments()->where('id', '!=', $this->payment->id)->sum('amount');
                $maxAllowed = $invoice->total_amount - $otherPayments;

                if ($amountInteger > $maxAllowed) {
                    $this->addError('amount', 'Jumlah pembayaran tidak boleh melebihi sisa tagihan: Rp ' . number_format($maxAllowed, 0, ',', '.'));
                    return;
                }

                // Handle attachment upload
                $attachmentPath = $this->payment->attachment_path;
                $attachmentName = $this->payment->attachment_name;

                if ($this->attachment) {
                    // Delete old attachment if exists
                    if ($attachmentPath && Storage::exists($attachmentPath)) {
                        Storage::delete($attachmentPath);
                    }

                    // Store new attachment
                    $attachmentPath = $this->attachment->store('payments', 'public');
                    $attachmentName = $this->attachment->getClientOriginalName();
                }

                // Update payment
                $this->payment->update([
                    'amount' => $amountInteger,
                    'payment_date' => $this->payment_date,
                    'payment_method' => $this->payment_method,
                    'bank_account_id' => $this->bank_account_id,
                    'reference_number' => $this->reference_number ?: null,
                    'attachment_path' => $attachmentPath,
                    'attachment_name' => $attachmentName,
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

            $this->toast()->success(__('common.success'), __('pages.payment_updated_successfully'))->send();
            $this->resetData();

            // Dispatch events
            $this->dispatch('payment-updated');
            $this->dispatch('invoice-updated');

        } catch (\Exception $e) {
            $this->toast()->error(__('common.error'), __('pages.payment_update_failed') . ': ' . $e->getMessage())->send();
        }
    }

    // Delete uploaded attachment
    public function deleteUpload(array $content): void
    {
        $this->attachment = null;
        $this->resetValidation('attachment');
    }

    // Delete existing attachment
    public function deleteExistingAttachment(): void
    {
        if (!$this->payment || !$this->payment->hasAttachment()) {
            return;
        }

        try {
            // Delete file from storage
            if (Storage::exists($this->payment->attachment_path)) {
                Storage::delete($this->payment->attachment_path);
            }

            // Update payment record
            $this->payment->update([
                'attachment_path' => null,
                'attachment_name' => null,
            ]);

            $this->payment->refresh();

            $this->toast()->success(__('common.success'), __('pages.attachment_deleted_successfully'))->send();

        } catch (\Exception $e) {
            $this->toast()->error(__('common.error'), __('pages.attachment_delete_failed'))->send();
        }
    }

    private function evaluateInvoiceStatus($invoice): string
    {
        $totalPaid = $invoice->payments()->sum('amount');
        $totalAmount = $invoice->total_amount;
        $dueDate = $invoice->due_date;

        if ($totalPaid >= $totalAmount && $totalPaid > 0) {
            return 'paid';
        }

        if ($totalPaid > 0 && $totalPaid < $totalAmount) {
            return 'partially_paid';
        }

        if ($totalPaid == 0) {
            return $dueDate->isPast() ? 'overdue' : 'sent';
        }

        return 'sent';
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