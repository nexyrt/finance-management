<?php

namespace App\Livewire\Reimbursements;

use App\Models\Reimbursement;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Show extends Component
{
    public bool $modal = false;
    public $reimbursementId = null;

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
            ? Reimbursement::with([
                'user',
                'reviewer',
                'payments.payer',
                'payments.bankTransaction.bankAccount'
            ])
                ->find($this->reimbursementId)
            : null;
    }

    public function editReimbursement(): void
    {
        $this->dispatch('edit::reimbursement', id: $this->reimbursementId);
        $this->modal = false;
    }

    public function reviewReimbursement(): void
    {
        $this->dispatch('review::reimbursement', id: $this->reimbursementId);
        $this->modal = false;
    }

    public function payReimbursement(): void
    {
        $this->dispatch('pay::reimbursement', id: $this->reimbursementId);
        $this->modal = false;
    }
}