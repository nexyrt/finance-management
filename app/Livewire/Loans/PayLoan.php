<?php

namespace App\Livewire\Loans;

use App\Livewire\Traits\Alert;
use App\Models\Loan;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\TransactionCategory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Livewire\Component;

class PayLoan extends Component
{
    use Alert;

    public bool $modal = false;
    public $loanId = null;
    public $loan = null;

    // Form fields
    public $bank_account_id = null;
    public $payment_date = null;
    public $principal_paid = null;
    public $interest_paid = null;
    public $reference_number = null;
    public $notes = null;

    public function render(): View
    {
        return view('livewire.loans.pay-loan');
    }

    #[On('load::pay-loan')]
    public function load(Loan $loan): void
    {
        $this->loanId = $loan->id;
        $this->loan = $loan;
        $this->payment_date = now()->format('Y-m-d');
        $this->modal = true;
    }

    #[Computed]
    public function bankAccounts(): array
    {
        return BankAccount::orderBy('account_name')
            ->get()
            ->map(fn($acc) => [
                'label' => "{$acc->account_name} - {$acc->bank_name}",
                'value' => $acc->id
            ])
            ->toArray();
    }

    #[Computed]
    public function remainingPrincipal(): int
    {
        if (!$this->loan)
            return 0;
        $totalPaid = $this->loan->payments()->sum('principal_paid');
        return $this->loan->principal_amount - $totalPaid;
    }

    #[Computed]
    public function remainingInterest(): int
    {
        if (!$this->loan)
            return 0;

        $totalInterest = $this->loan->interest_type === 'fixed'
            ? ($this->loan->interest_amount ?? 0)
            : (($this->loan->principal_amount * ($this->loan->interest_rate ?? 0) / 100 / 12) * $this->loan->term_months);

        $totalPaid = $this->loan->payments()->sum('interest_paid');
        return round($totalInterest) - $totalPaid;
    }

    public function rules(): array
    {
        return [
            'bank_account_id' => ['required', 'exists:bank_accounts,id'],
            'payment_date' => ['required', 'date'],
            'principal_paid' => ['nullable', 'numeric', 'min:0', 'max:' . $this->remainingPrincipal],
            'interest_paid' => ['nullable', 'numeric', 'min:0', 'max:' . $this->remainingInterest],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();

        // Validation: at least one must be filled
        if (empty($validated['principal_paid']) && empty($validated['interest_paid'])) {
            $this->error('Minimal salah satu harus diisi: Pembayaran Pokok atau Bunga');
            return;
        }

        $principalPaid = (int) ($validated['principal_paid'] ?? 0);
        $interestPaid = (int) ($validated['interest_paid'] ?? 0);

        // Create payment record
        $payment = $this->loan->payments()->create([
            'bank_account_id' => $validated['bank_account_id'],
            'payment_date' => $validated['payment_date'],
            'principal_paid' => $principalPaid,
            'interest_paid' => $interestPaid,
            'total_paid' => $principalPaid + $interestPaid,
            'reference_number' => $validated['reference_number'],
            'notes' => $validated['notes'],
        ]);

        // Bank transaction - Principal
        BankTransaction::create([
            'bank_account_id' => $validated['bank_account_id'],
            'amount' => $principalPaid,
            'transaction_type' => 'debit',
            'transaction_date' => $validated['payment_date'],
            'description' => "Pembayaran pokok pinjaman - {$this->loan->lender_name}",
            'reference_number' => $validated['reference_number'],
            'category_id' => TransactionCategory::where('code', 'FIN-LOAN-OUT')->first()->id,
        ]);

        // Bank transaction - Interest
        if ($interestPaid > 0) {
            BankTransaction::create([
                'bank_account_id' => $validated['bank_account_id'],
                'amount' => $interestPaid,
                'transaction_type' => 'debit',
                'transaction_date' => $validated['payment_date'],
                'description' => "Pembayaran bunga pinjaman - {$this->loan->lender_name}",
                'reference_number' => $validated['reference_number'],
                'category_id' => TransactionCategory::where('code', 'EXP-INTEREST')->first()->id,
            ]);
        }

        // Update loan status
        $newRemaining = $this->remainingPrincipal - $principalPaid;

        if ($newRemaining <= 0) {
            $this->loan->update(['status' => 'paid_off']);
        }

        $this->dispatch('paid');
        $this->reset();
        $this->success('Pembayaran berhasil dicatat');
    }
}