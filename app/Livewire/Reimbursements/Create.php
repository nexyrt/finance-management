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

    // Modal Control
    public bool $modal = false;

    // Form Fields
    public ?string $title = null;

    public ?string $description = null;

    public ?string $amount = null;

    public ?string $expense_date = null;

    public ?string $category = null;

    public $attachment = null;

    // Action Type
    public string $action = 'draft'; // 'draft' or 'submit'

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
            'amount' => ['required', 'string'],
            'expense_date' => ['required', 'date'],
            'category' => ['required', 'string', 'in:transport,meals,office_supplies,communication,accommodation,medical,other'],
            'attachment' => ['nullable', 'file', 'max:5120', 'mimes:jpg,jpeg,png,pdf'], // 5MB max
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();

        DB::transaction(function () use ($validated) {
            // Parse amount
            $amount = Reimbursement::parseAmount($validated['amount']);

            // Handle attachment upload
            $attachmentPath = null;
            $attachmentName = null;

            if ($this->attachment) {
                $attachmentPath = $this->attachment->store('reimbursements', 'public');
                $attachmentName = $this->attachment->getClientOriginalName();
            }

            // Create reimbursement
            $reimbursement = Reimbursement::create([
                'user_id' => auth()->id(),
                'title' => $validated['title'],
                'description' => $validated['description'],
                'amount' => $amount,
                'expense_date' => $validated['expense_date'],
                'category' => $validated['category'],
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachmentName,
                'status' => 'draft',
            ]);

            // If action is submit, auto-submit
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
