<?php

namespace App\Livewire\Reimbursements;

use App\Livewire\Traits\Alert;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\Reimbursement;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Payment extends Component
{
    use Alert;

    public bool $modal = false;
    public $reimbursementId = null;
    public $bankAccountId = null;
    public $paymentDate;
    public $paymentAmount = null;
    public $referenceNotes = null;
    public $isPartialPayment = false;

    public function mount(): void
    {
        if (!auth()->user()->can('pay reimbursements')) {
            abort(403, 'Unauthorized to process payments');
        }

        $this->paymentDate = now()->format('Y-m-d');
    }

    public function render(): View
    {
        return view('livewire.reimbursements.payment');
    }

    #[On('pay::reimbursement')]
    public function load(int $id): void
    {
        $reimbursement = Reimbursement::findOrFail($id);

        if (!$reimbursement->canPay()) {
            $this->error('Reimbursement harus berstatus Approved, memiliki kategori, dan belum lunas');
            return;
        }

        $this->reimbursementId = $id;
        $this->paymentDate = now()->format('Y-m-d');
        $this->isPartialPayment = false;
        $this->paymentAmount = null;
        $this->reset(['bankAccountId', 'referenceNotes']);

        $this->modal = true;
    }

    public function updatedIsPartialPayment($value): void
    {
        if (!$value && $this->reimbursement) {
            $this->paymentAmount = $this->reimbursement->amount_remaining;
        } else {
            $this->paymentAmount = null;
        }
    }

    #[Computed]
    public function reimbursement(): ?Reimbursement
    {
        return $this->reimbursementId
            ? Reimbursement::with(['user', 'category', 'payments.payer'])->find($this->reimbursementId)
            : null;
    }

    #[Computed]
    public function bankAccounts(): array
    {
        return BankAccount::orderBy('account_name')
            ->get()
            ->map(fn($bank) => [
                'label' => $bank->account_name . ' - ' . $bank->bank_name . ' (' . $bank->formatted_balance . ')',
                'value' => $bank->id,
            ])
            ->toArray();
    }

    public function rules(): array
    {
        $maxAmount = $this->reimbursement?->amount_remaining ?? 0;

        return [
            'bankAccountId' => ['required', 'exists:bank_accounts,id'],
            'paymentDate' => ['required', 'date', 'before_or_equal:today'],
            'paymentAmount' => ['required', 'integer', 'min:1', 'max:' . $maxAmount],
            'referenceNotes' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'bankAccountId.required' => 'Pilih rekening bank',
            'bankAccountId.exists' => 'Rekening bank tidak ditemukan',
            'paymentDate.required' => 'Tanggal pembayaran wajib diisi',
            'paymentDate.date' => 'Format tanggal tidak valid',
            'paymentDate.before_or_equal' => 'Tanggal pembayaran tidak boleh masa depan',
            'paymentAmount.required' => 'Jumlah pembayaran wajib diisi',
            'paymentAmount.min' => 'Jumlah pembayaran minimal Rp 1',
            'paymentAmount.max' => 'Jumlah pembayaran melebihi sisa tagihan',
        ];
    }

    public function processPayment(): void
    {
        if (!$this->isPartialPayment && $this->reimbursement) {
            $this->paymentAmount = $this->reimbursement->amount_remaining;
        }

        if (is_string($this->paymentAmount)) {
            $this->paymentAmount = Reimbursement::parseAmount($this->paymentAmount);
        }

        $validated = $this->validate();

        $reimbursement = Reimbursement::findOrFail($this->reimbursementId);

        if (!$reimbursement->canPay()) {
            $this->error('Reimbursement tidak dapat dibayar');
            return;
        }

        $bankAccount = BankAccount::findOrFail($validated['bankAccountId']);
        if ($bankAccount->balance < $validated['paymentAmount']) {
            $this->warning(
                'Peringatan: Saldo rekening tidak mencukupi. ' .
                'Saldo: ' . $bankAccount->formatted_balance . ', ' .
                'Dibutuhkan: Rp ' . number_format($validated['paymentAmount'], 0, ',', '.')
            );
        }

        DB::transaction(function () use ($reimbursement, $validated) {
            $isFullPayment = $validated['paymentAmount'] >= $reimbursement->amount_remaining;
            $paymentType = $isFullPayment ? 'Pelunasan' : 'Cicilan';

            // Use category_id from reimbursement (set by finance during review)
            $transaction = BankTransaction::create([
                'bank_account_id' => $validated['bankAccountId'],
                'amount' => $validated['paymentAmount'],
                'transaction_date' => $validated['paymentDate'],
                'transaction_type' => 'debit',
                'category_id' => $reimbursement->category_id,
                'description' => "{$paymentType} Reimbursement: {$reimbursement->title} - {$reimbursement->user->name}",
                'reference_number' => $validated['referenceNotes'],
            ]);

            $reimbursement->recordPayment(
                amount: $validated['paymentAmount'],
                bankTransactionId: $transaction->id,
                payerId: auth()->id(),
                paymentDate: $validated['paymentDate'],
                notes: $validated['referenceNotes']
            );
        });

        $this->dispatch('paid');

        if ($this->isPartialPayment) {
            $this->success('Pembayaran cicilan berhasil diproses');
        } else {
            $this->success('Pembayaran lunas berhasil diproses');
        }

        $this->reset();
    }
}