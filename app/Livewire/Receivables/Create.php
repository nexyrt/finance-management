<?php

namespace App\Livewire\Receivables;

use App\Livewire\Traits\Alert;
use App\Models\Client;
use App\Models\Receivable;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use Alert, WithFileUploads;

    public bool $modal = false;

    // Form Fields - NO TYPE DECLARATION
    public $type = 'employee_loan';
    public $debtor_id = null;
    public $principal_amount = null;
    public $interest_type = 'percentage'; // ← TAMBAH: default percentage
    public $interest_amount = null; // ← TAMBAH: untuk fixed interest
    public $interest_rate = 0; // ← DEFAULT: 0%
    public $installment_months = 1; // ← DEFAULT: 1 bulan
    public $loan_date = null; // ← Will be set in mount()
    public $purpose = null;
    public $notes = null;
    public $disbursement_account = null;
    public $contract_attachment = null;

    public function mount(): void
    {
        $this->loan_date = now()->format('Y-m-d'); // ← DEFAULT: today
    }

    public function render(): View
    {
        return view('livewire.receivables.create');
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

        if ($this->type === 'company_loan') {
            $rules['contract_attachment'] = ['required', 'file', 'max:5120', 'mimes:pdf,jpg,jpeg,png'];
        }

        return $rules;
    }

    public function save(): void
    {
        $validated = $this->validate();

        // Calculate installment amount
        $principalAmount = (int) $validated['principal_amount'];
        $interestRate = (float) ($validated['interest_rate'] ?? 0);
        $installmentMonths = (int) ($validated['installment_months'] ?? 1);

        $totalInterest = ($principalAmount * $interestRate / 100);
        $installmentAmount = round(($principalAmount + $totalInterest) / $installmentMonths);

        // Handle attachment
        $attachmentPath = null;
        $attachmentName = null;

        if ($this->contract_attachment) {
            $attachmentPath = $this->contract_attachment->store('receivables', 'public');
            $attachmentName = $this->contract_attachment->getClientOriginalName();
        }

        // Generate receivable number - FIX: Define before use
        $lastReceivable = Receivable::latest('id')->first();
        $nextNumber = $lastReceivable ? (int) substr($lastReceivable->receivable_number, 4) + 1 : 1;
        $receivableNumber = 'RCV-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        // Calculate due date
        $dueDate = now()->parse($validated['loan_date'])->addMonths($installmentMonths);

        // Create receivable
        Receivable::create([
            'receivable_number' => $receivableNumber, // Now defined
            'type' => $validated['type'],
            'debtor_type' => $this->type === 'employee_loan' ? User::class : Client::class,
            'debtor_id' => $validated['debtor_id'],
            'principal_amount' => $principalAmount,
            'interest_rate' => $interestRate,
            'installment_months' => $installmentMonths,
            'installment_amount' => $installmentAmount,
            'loan_date' => $validated['loan_date'],
            'due_date' => $dueDate,
            'status' => 'draft',
            'purpose' => $validated['purpose'],
            'notes' => $validated['notes'],
            'disbursement_account' => $validated['disbursement_account'],
            'contract_attachment_path' => $attachmentPath,
            'contract_attachment_name' => $attachmentName,
        ]);

        $this->dispatch('created');
        $this->reset();
        $this->success('Piutang berhasil dibuat');
    }
}