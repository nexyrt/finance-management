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

    // Modal Control
    public bool $modal = false;

    // Reimbursement ID
    public ?int $reimbursementId = null;

    // Payment Form
    public ?int $bankAccountId = null;

    public string $paymentDate;

    public ?string $referenceNotes = null;

    public function mount(): void
    {
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

        // Authorization check
        if (! auth()->user()->can('pay reimbursements')) {
            $this->error('Unauthorized action');

            return;
        }

        if (! $reimbursement->canPay()) {
            $this->error('This reimbursement cannot be paid');

            return;
        }

        $this->reimbursementId = $id;
        $this->paymentDate = now()->format('Y-m-d');
        $this->reset(['bankAccountId', 'referenceNotes']);

        $this->modal = true;
    }

    #[Computed]
    public function reimbursement(): ?Reimbursement
    {
        return $this->reimbursementId
            ? Reimbursement::with('user')->find($this->reimbursementId)
            : null;
    }

    #[Computed]
    public function bankAccounts(): array
    {
        return BankAccount::orderBy('account_name')
            ->get()
            ->map(fn ($bank) => [
                'label' => $bank->account_name.' - '.$bank->bank_name.' ('.$bank->formatted_balance.')',
                'value' => $bank->id,
            ])
            ->toArray();
    }

    public function rules(): array
    {
        return [
            'bankAccountId' => ['required', 'exists:bank_accounts,id'],
            'paymentDate' => ['required', 'date'],
            'referenceNotes' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'bankAccountId.required' => 'Please select a bank account',
            'paymentDate.required' => 'Payment date is required',
        ];
    }

    public function processPayment(): void
    {
        $validated = $this->validate();

        $reimbursement = Reimbursement::findOrFail($this->reimbursementId);

        if (! $reimbursement->canPay()) {
            $this->error('This reimbursement cannot be paid');

            return;
        }

        // Check bank account balance (optional warning)
        $bankAccount = BankAccount::findOrFail($validated['bankAccountId']);
        if ($bankAccount->balance < $reimbursement->amount) {
            $this->warning('Warning: Bank account balance is insufficient. Payment will still be processed.');
        }

        DB::transaction(function () use ($reimbursement, $validated) {
            // Extract category from review notes (if exists)
            $categoryId = null;
            if ($reimbursement->review_notes && preg_match('/Category: (.+?)\)/', $reimbursement->review_notes, $matches)) {
                // Try to find category by label from notes
                $categoryLabel = $matches[1];
                $category = \App\Models\TransactionCategory::where('label', $categoryLabel)->first();
                $categoryId = $category?->id;
            }

            // Create bank transaction (debit/expense)
            $transaction = BankTransaction::create([
                'bank_account_id' => $validated['bankAccountId'],
                'amount' => $reimbursement->amount,
                'transaction_date' => $validated['paymentDate'],
                'transaction_type' => 'debit',
                'category_id' => $categoryId,
                'description' => "Reimbursement: {$reimbursement->title} - {$reimbursement->user->name}",
                'reference_number' => $validated['referenceNotes'],
            ]);

            // Mark reimbursement as paid
            $reimbursement->markAsPaid(auth()->id(), $transaction->id);
        });

        $this->dispatch('paid');
        $this->success('Payment processed successfully');
        $this->reset();
    }
}
