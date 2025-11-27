<?php

namespace App\Livewire\Loans;

use App\Livewire\Traits\Alert;
use App\Models\Loan;
use App\Models\BankAccount;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class Update extends Component
{
    use Alert, WithFileUploads;

    public bool $modal = false;
    public $loanId = null;

    // Form fields
    public $loan_number = null;
    public $lender_name = null;
    public $principal_amount = null;
    public $interest_type = null;
    public $interest_amount = null;
    public $interest_rate = null;
    public $term_months = null;
    public $start_date = null;
    public $maturity_date = null;
    public $purpose = null;
    public $contract_attachment = null;
    public $currentAttachment = null;

    public function render(): View
    {
        return view('livewire.loans.update');
    }

    #[On('load::loan')]
    public function load(Loan $loan): void
    {
        $this->loanId = $loan->id;
        $this->loan_number = $loan->loan_number;
        $this->lender_name = $loan->lender_name;
        $this->principal_amount = number_format($loan->principal_amount, 0, ',', '.');
        $this->interest_type = $loan->interest_type;
        $this->interest_amount = $loan->interest_amount ? number_format($loan->interest_amount, 0, ',', '.') : null;
        $this->interest_rate = $loan->interest_rate;
        $this->term_months = $loan->term_months;
        $this->start_date = $loan->start_date->format('Y-m-d');
        $this->maturity_date = $loan->maturity_date->format('Y-m-d');
        $this->purpose = $loan->purpose;
        $this->currentAttachment = $loan->contract_attachment;

        $this->modal = true;
    }

    public function updatedTermMonths(): void
    {
        if ($this->start_date && $this->term_months) {
            $this->maturity_date = now()->parse($this->start_date)
                ->addMonths($this->term_months)
                ->format('Y-m-d');
        }
    }

    public function updatedStartDate(): void
    {
        if ($this->start_date && $this->term_months) {
            $this->maturity_date = now()->parse($this->start_date)
                ->addMonths($this->term_months)
                ->format('Y-m-d');
        }
    }

    public function rules(): array
    {
        return [
            'loan_number' => ['required', 'string', 'unique:loans,loan_number,' . $this->loanId],
            'lender_name' => ['required', 'string', 'max:255'],
            'principal_amount' => ['required', 'string'],
            'interest_type' => ['required', 'in:fixed,percentage'],
            'interest_amount' => ['required_if:interest_type,fixed', 'nullable', 'string'],
            'interest_rate' => ['required_if:interest_type,percentage', 'nullable', 'numeric', 'min:0', 'max:100'],
            'term_months' => ['required', 'integer', 'min:1'],
            'start_date' => ['required', 'date'],
            'maturity_date' => ['required', 'date', 'after:start_date'],
            'purpose' => ['nullable', 'string'],
            'contract_attachment' => ['nullable', 'file', 'max:5120', 'mimes:pdf,jpg,jpeg,png'],
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();
        $loan = Loan::findOrFail($this->loanId);

        $attachmentPath = $this->currentAttachment;
        if ($this->contract_attachment) {
            if ($loan->contract_attachment && \Storage::exists($loan->contract_attachment)) {
                \Storage::delete($loan->contract_attachment);
            }
            $attachmentPath = $this->contract_attachment->store('loans', 'public');
        }

        $principalAmount = (int) preg_replace('/[^0-9]/', '', $validated['principal_amount']);
        $interestAmount = $validated['interest_type'] === 'fixed'
            ? (int) preg_replace('/[^0-9]/', '', $validated['interest_amount'])
            : null;

        $loan->update([
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
            'purpose' => $validated['purpose'],
            'contract_attachment' => $attachmentPath,
        ]);

        $this->dispatch('updated');
        $this->reset();
        $this->success('Pinjaman berhasil diperbarui');
    }
}