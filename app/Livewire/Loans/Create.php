<?php

namespace App\Livewire\Loans;

use App\Livewire\Traits\Alert;
use App\Models\Loan;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\TransactionCategory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use Alert, WithFileUploads;

    public bool $modal = false;

    // Form fields - NO TYPE DECLARATION
    public $loan_number = null;
    public $lender_name = null;
    public $principal_amount = null;
    public $interest_type = 'percentage';
    public $interest_amount = null;
    public $interest_rate = null;
    public $term_months = null;
    public $start_date = null;
    public $maturity_date = null;
    public $purpose = null;
    public $contract_attachment = null;
    public $bank_account_id = null;

    public function mount(): void
    {
        // Generate loan number
        $latest = Loan::latest('id')->first();
        $nextId = $latest ? $latest->id + 1 : 1;
        $this->loan_number = 'LOAN-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);

        $this->start_date = now()->format('Y-m-d');
    }

    public function render(): View
    {
        return view('livewire.loans.create');
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

    public function updatedTermMonths(): void
    {
        if ($this->start_date && $this->term_months) {
            $this->maturity_date = now()->parse($this->start_date)
                ->addMonths((int) $this->term_months) // Cast to int
                ->format('Y-m-d');
        }
    }

    public function updatedStartDate(): void
    {
        if ($this->start_date && $this->term_months) {
            $this->maturity_date = now()->parse($this->start_date)
                ->addMonths((int) $this->term_months) // Cast to int
                ->format('Y-m-d');
        }
    }

    // app/Livewire/Loans/Create.php

    public function rules(): array
    {
        return [
            'loan_number' => ['required', 'string', 'unique:loans,loan_number'],
            'lender_name' => ['required', 'string', 'max:255'],
            'principal_amount' => ['required', 'numeric', 'min:0'], // Changed from string
            'interest_type' => ['required', 'in:fixed,percentage'],
            'interest_amount' => ['nullable', 'numeric', 'min:0'], // Remove required_if
            'interest_rate' => ['nullable', 'numeric', 'min:0', 'max:100'], // Remove required_if
            'term_months' => ['required', 'integer', 'min:1'],
            'start_date' => ['required', 'date'],
            'maturity_date' => ['required', 'date', 'after:start_date'],
            'purpose' => ['nullable', 'string'],
            'contract_attachment' => ['nullable', 'file', 'max:5120', 'mimes:pdf,jpg,jpeg,png'],
            'bank_account_id' => ['required', 'exists:bank_accounts,id'],
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();

        $attachmentPath = null;
        if ($this->contract_attachment) {
            $attachmentPath = $this->contract_attachment->store('loans', 'public');
        }

        // WireUI Currency already returns clean number, just cast to int
        $principalAmount = (int) $validated['principal_amount'];
        $interestAmount = $validated['interest_type'] === 'fixed'
            ? (int) $validated['interest_amount']
            : null;

        $loan = Loan::create([
            'loan_number' => $validated['loan_number'],
            'lender_name' => $validated['lender_name'],
            'principal_amount' => $principalAmount,
            'interest_type' => $validated['interest_type'],
            'interest_amount' => $interestAmount,
            'interest_rate' => $validated['interest_type'] === 'percentage'
                ? $validated['interest_rate']
                : null,
            'term_months' => $validated['term_months'],
            'start_date' => $validated['start_date'],
            'maturity_date' => $validated['maturity_date'],
            'status' => 'active',
            'purpose' => $validated['purpose'],
            'contract_attachment' => $attachmentPath,
        ]);

        // Record bank transaction
        BankTransaction::create([
            'bank_account_id' => $validated['bank_account_id'],
            'amount' => $principalAmount,
            'transaction_type' => 'credit',
            'transaction_date' => $validated['start_date'],
            'description' => "Penerimaan pinjaman dari {$validated['lender_name']}",
            'reference_number' => $validated['loan_number'],
            'category_id' => TransactionCategory::where('code', 'FIN-LOAN-IN')->first()->id,
        ]);

        $this->dispatch('created');
        $this->reset();
        $this->mount();
        $this->success('Pinjaman berhasil dibuat');
    }
}