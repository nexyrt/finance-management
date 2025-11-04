<?php

namespace App\Livewire\Reimbursements;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class Index extends Component
{
    public bool $workflowGuideModal = false;

    public function render(): View
    {
        return view('livewire.reimbursements.index');
    }

    // Check if user can view all requests (Finance)
    public function canViewAllRequests(): bool
    {
        return auth()->check() && auth()->user()->can('approve reimbursements');
    }

    // Check if user can create reimbursements
    public function canCreate(): bool
    {
        return auth()->check() && auth()->user()->can('create reimbursements');
    }
}
