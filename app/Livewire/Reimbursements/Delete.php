<?php

namespace App\Livewire\Reimbursements;

use App\Livewire\Traits\Alert;
use App\Models\Reimbursement;
use Livewire\Attributes\Renderless;
use Livewire\Component;

class Delete extends Component
{
    use Alert;

    public Reimbursement $reimbursement;

    // Inline render - No blade file needed
    public function render(): string
    {
        return <<<'HTML'
        <div>
            <x-button.circle icon="trash" color="red" size="sm" wire:click="confirm" title="Delete" />
        </div>
        HTML;
    }

    // Step 1: Confirmation dialog
    #[Renderless]
    public function confirm(): void
    {
        // Authorization check
        if ($this->reimbursement->user_id !== auth()->id()) {
            $this->error('Unauthorized action');

            return;
        }

        if (! $this->reimbursement->canDelete()) {
            $this->error('Cannot delete this reimbursement');

            return;
        }

        $this->question('Delete reimbursement?', 'This action cannot be undone.')
            ->confirm(method: 'delete')
            ->cancel()
            ->send();
    }

    // Step 2: Execute delete
    public function delete(): void
    {
        // Authorization check
        if ($this->reimbursement->user_id !== auth()->id()) {
            $this->error('Unauthorized action');

            return;
        }

        if (! $this->reimbursement->canDelete()) {
            $this->error('Cannot delete this reimbursement');

            return;
        }

        $this->reimbursement->delete();

        $this->dispatch('deleted');
        $this->success('Reimbursement deleted successfully');
    }
}
