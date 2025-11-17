<?php

namespace App\Livewire\Reimbursements;

use App\Livewire\Traits\Alert;
use App\Models\Reimbursement;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use Alert, WithFileUploads;

    public bool $modal = false;
    public $title = null;
    public $description = null;
    public $amount = null;
    public $expense_date = null;
    public $category = null;
    public $attachment = null;
    public $action = 'draft';

    public function mount(): void
    {
        $this->expense_date = now()->format('Y-m-d');
    }

    public function render(): View
    {
        return view('livewire.reimbursements.create');
    }

    #[Computed]
    public function categories(): array
    {
        return Reimbursement::categories();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'amount' => ['required'], // Remove 'string' - will be parsed
            'expense_date' => ['required', 'date'],
            'category' => ['nullable', 'string', 'in:transport,meals,office_supplies,communication,accommodation,medical,other'],
            'attachment' => ['nullable', 'file', 'max:5120', 'mimes:jpg,jpeg,png,pdf'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'Amount is required',
        ];
    }

    public function save(): void
    {
        // Parse amount before validation
        if ($this->amount) {
            $parsedAmount = Reimbursement::parseAmount($this->amount);
            if ($parsedAmount < 1) {
                $this->addError('amount', 'Amount must be at least Rp 1');
                return;
            }
        }

        $validated = $this->validate();

        DB::transaction(function () use ($validated) {
            $amount = Reimbursement::parseAmount($validated['amount']);

            $attachmentPath = null;
            $attachmentName = null;

            if ($this->attachment) {
                $attachmentPath = $this->attachment->store('reimbursements', 'public');
                $attachmentName = $this->attachment->getClientOriginalName();
            }

            $reimbursement = Reimbursement::create([
                'user_id' => auth()->id(),
                'title' => $validated['title'],
                'description' => $validated['description'],
                'amount' => $amount,
                'expense_date' => $validated['expense_date'],
                'category_input' => $validated['category'],
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachmentName,
                'status' => 'draft',
            ]);

            if ($this->action === 'submit') {
                $reimbursement->submit();
            }
        });

        $this->dispatch('created');
        $this->reset();

        if ($this->action === 'submit') {
            $this->success('Reimbursement submitted for approval');
        } else {
            $this->success('Reimbursement saved as draft');
        }
    }

    public function saveAsDraft(): void
    {
        $this->action = 'draft';
        $this->save();
    }

    public function submitForApproval(): void
    {
        $this->action = 'submit';
        $this->save();
    }
}