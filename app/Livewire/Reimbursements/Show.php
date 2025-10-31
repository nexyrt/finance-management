<?php

namespace App\Livewire\Reimbursements;

use App\Models\Reimbursement;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Show extends Component
{
    // Modal Control
    public bool $modal = false;

    // Reimbursement ID
    public ?int $reimbursementId = null;

    public function render(): View
    {
        return view('livewire.reimbursements.show');
    }

    #[On('load::reimbursement')]
    public function load(int $id): void
    {
        $this->reimbursementId = $id;
        $this->modal = true;
    }

    #[Computed]
    public function reimbursement(): ?Reimbursement
    {
        return $this->reimbursementId 
            ? Reimbursement::with(['user', 'reviewer', 'payer', 'bankTransaction.bankAccount'])
                ->find($this->reimbursementId)
            : null;
    }

    public function closeModal(): void
    {
        $this->modal = false;
    }

    // Quick actions from show modal
    public function editReimbursement(): void
    {
        $this->dispatch('edit::reimbursement', id: $this->reimbursementId);
        $this->closeModal();
    }

    public function reviewReimbursement(): void
    {
        $this->dispatch('review::reimbursement', id: $this->reimbursementId);
        $this->closeModal();
    }

    public function payReimbursement(): void
    {
        $this->dispatch('pay::reimbursement', id: $this->reimbursementId);
        $this->closeModal();
    }
}