<?php

namespace App\Livewire\Reimbursements;

use App\Livewire\Traits\Alert;
use App\Models\Reimbursement;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class Update extends Component
{
    use Alert, WithFileUploads;

    // Modal Control
    public bool $modal = false;

    // Reimbursement ID
    public ?int $reimbursementId = null;

    // Form Fields
    public ?string $title = null;

    public ?string $description = null;

    public ?string $amount = null;

    public ?string $expense_date = null;

    public ?string $category = null;

    public $attachment = null;

    public ?string $existingAttachment = null;

    public bool $removeAttachment = false;

    // Action Type
    public string $action = 'draft'; // 'draft' or 'submit'

    public function render(): View
    {
        return view('livewire.reimbursements.update');
    }

    #[On('edit::reimbursement')]
    public function load(int $id): void
    {
        $reimbursement = Reimbursement::findOrFail($id);

        // Authorization check
        if ($reimbursement->user_id !== auth()->id()) {
            $this->error('Unauthorized action');

            return;
        }

        // Can only edit draft or rejected
        if (! $reimbursement->canEdit()) {
            $this->error('Cannot edit this reimbursement');

            return;
        }

        $this->reimbursementId = $reimbursement->id;
        $this->title = $reimbursement->title;
        $this->description = $reimbursement->description;
        $this->amount = number_format($reimbursement->amount, 0, ',', '.');
        $this->expense_date = $reimbursement->expense_date->format('Y-m-d');
        $this->category = $reimbursement->category;
        $this->existingAttachment = $reimbursement->attachment_name;

        $this->modal = true;
    }

    #[Computed]
    public function categories(): array
    {
        return Reimbursement::categories();
    }

    #[Computed]
    public function reimbursement(): ?Reimbursement
    {
        return $this->reimbursementId
            ? Reimbursement::find($this->reimbursementId)
            : null;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'amount' => ['required', 'string'],
            'expense_date' => ['required', 'date'],
            'category' => ['required', 'string', 'in:transport,meals,office_supplies,communication,accommodation,medical,other'],
            'attachment' => ['nullable', 'file', 'max:5120', 'mimes:jpg,jpeg,png,pdf'],
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();

        $reimbursement = Reimbursement::findOrFail($this->reimbursementId);

        // Authorization check
        if ($reimbursement->user_id !== auth()->id()) {
            $this->error('Unauthorized action');

            return;
        }

        if (! $reimbursement->canEdit()) {
            $this->error('Cannot edit this reimbursement');

            return;
        }

        DB::transaction(function () use ($reimbursement, $validated) {
            // Parse amount
            $amount = Reimbursement::parseAmount($validated['amount']);

            // Handle attachment
            $attachmentPath = $reimbursement->attachment_path;
            $attachmentName = $reimbursement->attachment_name;

            // Remove existing attachment if requested
            if ($this->removeAttachment && $attachmentPath) {
                if (Storage::disk('public')->exists($attachmentPath)) {
                    Storage::disk('public')->delete($attachmentPath);
                }
                $attachmentPath = null;
                $attachmentName = null;
            }

            // Upload new attachment
            if ($this->attachment) {
                // Delete old attachment if exists
                if ($attachmentPath && Storage::disk('public')->exists($attachmentPath)) {
                    Storage::disk('public')->delete($attachmentPath);
                }

                $attachmentPath = $this->attachment->store('reimbursements', 'public');
                $attachmentName = $this->attachment->getClientOriginalName();
            }

            // Update reimbursement
            $reimbursement->update([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'amount' => $amount,
                'expense_date' => $validated['expense_date'],
                'category' => $validated['category'],
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachmentName,
            ]);

            // If action is submit, submit for approval
            if ($this->action === 'submit') {
                $reimbursement->submit();
            }
        });

        $this->dispatch('updated');
        $this->reset();

        if ($this->action === 'submit') {
            $this->success('Reimbursement submitted for approval');
        } else {
            $this->success('Reimbursement updated successfully');
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

    public function removeExistingAttachment(): void
    {
        $this->removeAttachment = true;
        $this->existingAttachment = null;
    }
}
