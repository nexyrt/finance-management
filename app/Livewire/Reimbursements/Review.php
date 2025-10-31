<?php

namespace App\Livewire\Reimbursements;

use App\Livewire\Traits\Alert;
use App\Models\Reimbursement;
use App\Models\TransactionCategory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;
use Livewire\Component;

class Review extends Component
{
    use Alert;

    // Modal Control
    public bool $modal = false;

    // Reimbursement ID
    public ?int $reimbursementId = null;

    // Review Form
    public $decision = 'approve'; // 'approve' or 'reject'
    public ?int $categoryId = null; // Transaction category (required if approve)
    public ?string $notes = null;

    public function render(): View
    {
        return view('livewire.reimbursements.review');
    }

    #[On('review::reimbursement')]
    public function load(int $id): void
    {
        $reimbursement = Reimbursement::findOrFail($id);

        // Authorization check
        if (!auth()->user()->can('approve reimbursements')) {
            $this->error('Unauthorized action');
            return;
        }

        if (!$reimbursement->canReview()) {
            $this->error('This reimbursement cannot be reviewed');
            return;
        }

        $this->reimbursementId = $id;
        $this->decision = 'approve'; // Default to approve
        $this->reset(['categoryId', 'notes']);

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
    public function transactionCategories(): array
    {
        // Get expense categories (parent + children)
        return TransactionCategory::ofType('expense')
            ->parents()
            ->with('children')
            ->orderBy('label')
            ->get()
            ->flatMap(function ($parent) {
                $categories = [];
                
                // Add parent category
                $categories[] = [
                    'label' => $parent->label,
                    'value' => $parent->id,
                ];
                
                // Add children categories with indentation
                foreach ($parent->children as $child) {
                    $categories[] = [
                        'label' => '  └─ ' . $child->label,
                        'value' => $child->id,
                    ];
                }
                
                return $categories;
            })
            ->toArray();
    }

    public function rules(): array
    {
        return [
            'decision' => ['required', 'in:approve,reject'],
            'categoryId' => ['required_if:decision,approve', 'nullable', 'exists:transaction_categories,id'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'categoryId.required_if' => 'Transaction category is required when approving',
            'notes.max' => 'Notes cannot exceed 500 characters',
        ];
    }

    public function submitReview(): void
    {
        $validated = $this->validate();

        $reimbursement = Reimbursement::findOrFail($this->reimbursementId);

        if (!$reimbursement->canReview()) {
            $this->error('This reimbursement cannot be reviewed');
            return;
        }

        if ($validated['decision'] === 'approve') {
            // Approve with category
            $category = TransactionCategory::findOrFail($validated['categoryId']);
            
            $notes = $validated['notes'] 
                ? $validated['notes'] . " (Category: {$category->label})" 
                : "Approved - Category: {$category->label}";

            $reimbursement->approve(auth()->id(), $notes);
            
            $this->dispatch('reviewed');
            $this->success('Reimbursement approved successfully');
        } else {
            // Reject
            if (empty($validated['notes'])) {
                $this->error('Please provide a reason for rejection');
                return;
            }

            $reimbursement->reject(auth()->id(), $validated['notes']);
            
            $this->dispatch('reviewed');
            $this->warning('Reimbursement rejected');
        }

        $this->reset();
    }

    // Quick actions
    public function approveQuick(): void
    {
        $this->decision = 'approve';
    }

    public function rejectQuick(): void
    {
        $this->decision = 'reject';
    }
}