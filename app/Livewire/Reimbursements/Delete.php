<?php

namespace App\Livewire\Reimbursements;

use App\Models\Reimbursement;
use Livewire\Attributes\Renderless;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Delete extends Component
{
    use Interactions;

    public Reimbursement $reimbursement;

    public function render()
    {
        return view('livewire.reimbursements.delete');
    }

    // Step 1: Confirmation dialog
    #[Renderless]
    public function confirm(): void
    {
        \Log::info('Delete confirm called for reimbursement: ' . $this->reimbursement->id);

        // Authorization check - Owner or Admin only
        $user = auth()->user();
        if ($this->reimbursement->user_id !== $user->id && !$user->hasRole('admin')) {
            $this->toast()->error('Error', 'Unauthorized action')->send();
            return;
        }

        if (! $this->reimbursement->canDelete($user)) {
            $this->toast()->error('Error', 'Cannot delete this reimbursement')->send();
            return;
        }

        $this->dialog()
            ->question('Hapus Reimbursement?', 'Tindakan ini tidak dapat dibatalkan.')
            ->confirm('Ya, Hapus', 'delete', 'Reimbursement berhasil dihapus')
            ->cancel('Batal')
            ->send();
    }

    // Step 2: Execute delete
    public function delete(): void
    {
        // Authorization check - Owner or Admin only
        $user = auth()->user();
        if ($this->reimbursement->user_id !== $user->id && !$user->hasRole('admin')) {
            $this->toast()->error('Error', 'Unauthorized action')->send();
            return;
        }

        if (! $this->reimbursement->canDelete($user)) {
            $this->toast()->error('Error', 'Cannot delete this reimbursement')->send();
            return;
        }

        $this->reimbursement->delete();

        $this->toast()->success('Berhasil', 'Reimbursement berhasil dihapus')->send();
        $this->dispatch('deleted');
    }
}
