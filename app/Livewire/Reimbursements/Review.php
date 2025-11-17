<?php

namespace App\Livewire\Reimbursements;

use App\Livewire\Traits\Alert;
use App\Models\Reimbursement;
use App\Models\TransactionCategory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Review extends Component
{
    use Alert;

    public bool $modal = false;
    public $reimbursementId = null;
    public $action = null; // 'approve' or 'reject'

    // Form fields
    public $categoryId = null;
    public $reviewNotes = null;

    public function render(): View
    {
        return view('livewire.reimbursements.review');
    }

    #[On('review::reimbursement')]
    public function load(int $id): void
    {
        if (!auth()->user()->can('approve reimbursements')) {
            $this->error('Unauthorized action');
            return;
        }

        $reimbursement = Reimbursement::findOrFail($id);

        if (!$reimbursement->canReview()) {
            $this->error('Reimbursement harus berstatus Pending untuk direview');
            return;
        }

        $this->reimbursementId = $id;
        $this->action = null;
        $this->reset(['categoryId', 'reviewNotes']);
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
    public function expenseCategories(): array
    {
        return TransactionCategory::where('type', 'expense')
            ->orderBy('label')
            ->get()
            ->map(fn($cat) => ['label' => $cat->label, 'value' => $cat->id])
            ->toArray();
    }

    public function rules(): array
    {
        $rules = ['reviewNotes' => ['nullable', 'string', 'max:500']];

        if ($this->action === 'approve') {
            $rules['categoryId'] = ['required', 'exists:transaction_categories,id'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'categoryId.required' => 'Pilih kategori transaksi',
            'categoryId.exists' => 'Kategori tidak valid',
        ];
    }

    public function approveReimbursement(): void
    {
        $this->action = 'approve';
        $this->processReview();
    }

    public function rejectReimbursement(): void
    {
        $this->action = 'reject';
        $this->processReview();
    }

    private function processReview(): void
    {
        $validated = $this->validate();

        $reimbursement = Reimbursement::findOrFail($this->reimbursementId);

        if (!$reimbursement->canReview()) {
            $this->error('Reimbursement tidak dapat direview');
            return;
        }

        if ($this->action === 'approve') {
            // Set category then approve
            $reimbursement->update(['category_id' => $validated['categoryId']]);
            $reimbursement->approve(auth()->id(), $validated['reviewNotes']);
            $this->success('Reimbursement disetujui');
        } else {
            $reimbursement->reject(auth()->id(), $validated['reviewNotes']);
            $this->success('Reimbursement ditolak');
        }

        $this->dispatch('reviewed');
        $this->reset();
    }
}