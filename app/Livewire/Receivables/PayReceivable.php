<?php

namespace App\Livewire\Receivables;

use App\Livewire\Traits\Alert;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\Receivable;
use App\Models\ReceivablePayment;
use App\Models\TransactionCategory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class PayReceivable extends Component
{
    use Alert;

    public bool $modal = false;

    public $receivableId = null;
    public $receivable = null;

    // Form Fields - NO TYPE DECLARATION
    public $bank_account_id = null; // ← TAMBAH
    public $payment_date = null;
    public $principal_paid = null;
    public $interest_paid = null;
    public $payment_method = 'bank_transfer';
    public $reference_number = null;
    public $notes = null;

    public function mount(): void
    {
        $this->payment_date = now()->format('Y-m-d');
    }

    public function render(): View
    {
        return view('livewire.receivables.pay-receivable');
    }

    #[On('load::pay-receivable')]
    public function load(Receivable $receivable): void
    {
        $this->receivableId = $receivable->id;
        $this->receivable = $receivable;
        $this->bank_account_id = null; // ← RESET
        $this->payment_date = now()->format('Y-m-d');
        $this->principal_paid = null;
        $this->interest_paid = null;
        $this->payment_method = 'bank_transfer';
        $this->reference_number = null;
        $this->notes = null;

        $this->modal = true;
    }

    #[Computed]
    public function bankAccounts(): array
    {
        return BankAccount::orderBy('account_name')
            ->get()
            ->map(fn($account) => [
                'label' => "{$account->account_name} - {$account->bank_name}",
                'value' => $account->id,
            ])
            ->toArray();
    }

    #[Computed]
    public function remainingPrincipal(): int
    {
        if (!$this->receivable)
            return 0;
        $totalPaid = $this->receivable->payments()->sum('principal_paid');
        return $this->receivable->principal_amount - $totalPaid;
    }

    #[Computed]
    public function remainingInterest(): int
    {
        if (!$this->receivable)
            return 0;

        $totalInterest = round($this->receivable->principal_amount * $this->receivable->interest_rate / 100);
        $totalPaid = $this->receivable->payments()->sum('interest_paid');

        return $totalInterest - $totalPaid;
    }

    public function rules(): array
    {
        $rules = [
            'payment_date' => ['required', 'date'],
            'principal_paid' => ['nullable', 'numeric', 'min:0', 'max:' . $this->remainingPrincipal],
            'interest_paid' => ['nullable', 'numeric', 'min:0', 'max:' . $this->remainingInterest],
            'payment_method' => ['required', 'in:cash,payroll_deduction,bank_transfer'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];

        // Bank account required only for bank_transfer
        if ($this->payment_method === 'bank_transfer') {
            $rules['bank_account_id'] = ['required', 'exists:bank_accounts,id'];
        }

        return $rules;
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
        $totalPaid = $principalPaid + $interestPaid;

        // Create payment record
        ReceivablePayment::create([
            'receivable_id' => $this->receivable->id,
            'payment_date' => $validated['payment_date'],
            'principal_paid' => $principalPaid,
            'interest_paid' => $interestPaid,
            'total_paid' => $totalPaid,
            'payment_method' => $validated['payment_method'],
            'reference_number' => $validated['reference_number'],
            'notes' => $validated['notes'],
        ]);

        // Create bank transactions if bank_transfer
        if ($validated['payment_method'] === 'bank_transfer') {
            $categoryPrincipal = TransactionCategory::where('code', 'FIN-RCV-IN')->first();
            $categoryInterest = TransactionCategory::where('code', 'REV-INTEREST')->first();

            // Principal payment (if any)
            if ($principalPaid > 0) {
                BankTransaction::create([
                    'bank_account_id' => $validated['bank_account_id'], // ← GUNAKAN dari form
                    'amount' => $principalPaid,
                    'transaction_date' => $validated['payment_date'],
                    'transaction_type' => 'credit',
                    'description' => "Pembayaran piutang pokok: {$this->receivable->receivable_number} - {$this->receivable->debtor?->name}",
                    'reference_number' => $validated['reference_number'],
                    'category_id' => $categoryPrincipal?->id,
                ]);
            }

            // Interest payment (if any)
            if ($interestPaid > 0) {
                BankTransaction::create([
                    'bank_account_id' => $validated['bank_account_id'], // ← GUNAKAN dari form
                    'amount' => $interestPaid,
                    'transaction_date' => $validated['payment_date'],
                    'transaction_type' => 'credit',
                    'description' => "Pembayaran bunga piutang: {$this->receivable->receivable_number} - {$this->receivable->debtor?->name}",
                    'reference_number' => $validated['reference_number'],
                    'category_id' => $categoryInterest?->id,
                ]);
            }
        }

        // Update receivable status
        $newRemainingPrincipal = $this->remainingPrincipal - $principalPaid;

        if ($newRemainingPrincipal <= 0) {
            $this->receivable->update(['status' => 'paid_off']);
        }

        $this->dispatch('paid');
        $this->reset();
        $this->payment_date = now()->format('Y-m-d');
        $this->success('Pembayaran berhasil dicatat');
    }
}