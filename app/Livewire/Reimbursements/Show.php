<?php

namespace App\Livewire\Reimbursements;

use App\Models\Reimbursement;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class Show extends Component
{
    public ?Reimbursement $reimbursement = null;
    public bool $modal = false;

    public function render(): View
    {
        return view('livewire.reimbursements.show');
    }

    #[On('show::reimbursement')]
    public function load(Reimbursement $reimbursement): void
    {
        $this->reimbursement = $reimbursement->load(['user', 'reviewer', 'payer', 'bankTransaction']);
        $this->modal = true;
    }
}