<?php

namespace App\Livewire\Receivables;

use App\Livewire\Traits\Alert;
use App\Models\Client;
use App\Models\Receivable;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class Update extends Component
{
    use Alert, WithFileUploads;

    public bool $modal = false;

    // Receivable ID
    public $receivableId = null;

    // Form Fields - NO TYPE DECLARATION
    public $type = null;
    public $debtor_id = null;
    public $principal_amount = null;
    public $interest_rate = null;
    public $installment_months = null;
    public $loan_date = null;
    public $purpose = null;
    public $notes = null;
    public $contract_attachment = null;
    public $currentAttachment = null;
    public $disbursement_account = null; // ← TAMBAH
    public $interest_type = 'percentage'; // ← TAMBAH
    public $interest_amount = null; // ← TAMBAH

    public function render(): View
    {
        return view('livewire.receivables.update');
    }

    #[On('load::receivable')]
    public function load(Receivable $receivable): void
    {
        $this->receivableId = $receivable->id;
        $this->type = $receivable->type;
        $this->debtor_id = $receivable->debtor_id;
        $this->principal_amount = $receivable->principal_amount;
        $this->interest_rate = $receivable->interest_rate;

        // Detect interest type from data
        // If interest_rate is whole number and matches calculation, likely fixed
        $calculatedFixed = round($receivable->principal_amount * $receivable->interest_rate / 100);
        $this->interest_type = 'percentage'; // Default
        $this->interest_amount = 0;

        $this->installment_months = $receivable->installment_months;
        $this->loan_date = $receivable->loan_date?->format('Y-m-d');
        $this->purpose = $receivable->purpose;
        $this->notes = $receivable->notes;
        $this->disbursement_account = $receivable->disbursement_account;
        $this->currentAttachment = $receivable->contract_attachment_path;

        $this->modal = true;
    }

    public function rules(): array
    {
        $rules = [
            'type' => ['required', 'in:employee_loan,company_loan'],
            'debtor_id' => ['required', 'integer'],
            'principal_amount' => ['required', 'numeric', 'min:1'],
            'interest_type' => ['required', 'in:fixed,percentage'], // ← TAMBAH
            'interest_amount' => ['nullable', 'numeric', 'min:0'], // ← TAMBAH
            'interest_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'installment_months' => ['nullable', 'integer', 'min:1'],
            'loan_date' => ['required', 'date'],
            'purpose' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'disbursement_account' => ['required', 'string', 'max:255'],
            'contract_attachment' => ['nullable', 'file', 'max:5120', 'mimes:pdf,jpg,jpeg,png'],
        ];

        if ($this->type === 'employee_loan') {
            $rules['principal_amount'][] = 'max:10000000';
        }

        return $rules;
    }

    public function save(): void
    {
        $validated = $this->validate();

        $receivable = Receivable::findOrFail($this->receivableId);

        $principalAmount = (int) $validated['principal_amount'];
        $installmentMonths = (int) ($validated['installment_months'] ?? 1);

        // Calculate interest based on type
        $interestRate = 0;
        $totalInterest = 0;

        if ($this->interest_type === 'fixed') {
            $totalInterest = (int) ($validated['interest_amount'] ?? 0);
            $interestRate = $principalAmount > 0 ? ($totalInterest / $principalAmount * 100) : 0;
        } else {
            $interestRate = (float) ($validated['interest_rate'] ?? 0);
            $totalInterest = round($principalAmount * $interestRate / 100);
        }

        $installmentAmount = round(($principalAmount + $totalInterest) / $installmentMonths);

        // Calculate due date
        $dueDate = now()->parse($validated['loan_date'])->addMonths($installmentMonths);

        // Handle attachment
        if ($this->contract_attachment) {
            if ($receivable->contract_attachment_path && Storage::exists($receivable->contract_attachment_path)) {
                Storage::delete($receivable->contract_attachment_path);
            }

            $attachmentPath = $this->contract_attachment->store('receivables', 'public');
            $attachmentName = $this->contract_attachment->getClientOriginalName();

            $receivable->update([
                'contract_attachment_path' => $attachmentPath,
                'contract_attachment_name' => $attachmentName,
            ]);
        }

        // Update receivable
        $receivable->update([
            'type' => $validated['type'],
            'debtor_type' => $this->type === 'employee_loan' ? User::class : Client::class,
            'debtor_id' => $validated['debtor_id'],
            'principal_amount' => $principalAmount,
            'interest_rate' => $interestRate,
            'installment_months' => $installmentMonths,
            'installment_amount' => $installmentAmount,
            'loan_date' => $validated['loan_date'],
            'due_date' => $dueDate,
            'purpose' => $validated['purpose'],
            'notes' => $validated['notes'],
            'disbursement_account' => $validated['disbursement_account'],
            'status' => 'draft',
            'rejection_reason' => null,
            'approved_by' => null,
            'approved_at' => null,
        ]);

        $this->dispatch('updated');
        $this->reset();
        $this->success('Piutang berhasil diperbarui dan dikembalikan ke draft');
    }

    #[Computed]
    public function employees(): array
    {
        return User::where('status', 'active')
            ->orderBy('name')
            ->get()
            ->map(fn($user) => [
                'label' => $user->name,
                'value' => $user->id,
            ])
            ->toArray();
    }

    #[Computed]
    public function companies(): array
    {
        return Client::where('type', 'company')
            ->where('status', 'Active')
            ->orderBy('name')
            ->get()
            ->map(fn($client) => [
                'label' => $client->name,
                'value' => $client->id,
            ])
            ->toArray();
    }

    public function removeAttachment(): void
    {
        $receivable = Receivable::findOrFail($this->receivableId);

        if ($receivable->contract_attachment_path && Storage::exists($receivable->contract_attachment_path)) {
            Storage::delete($receivable->contract_attachment_path);
        }

        $receivable->update([
            'contract_attachment_path' => null,
            'contract_attachment_name' => null,
        ]);

        $this->currentAttachment = null;
        $this->success('Dokumen berhasil dihapus');
    }
}