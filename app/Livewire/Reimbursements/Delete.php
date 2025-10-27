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

    public function render(): string
    {
        return <<<'HTML'
        <div>
            <x-button.circle icon="trash" color="red" size="sm" wire:click="confirm" title="Hapus" />
        </div>
        HTML;
    }

    #[Renderless]
    public function confirm(): void
    {
        $this->question('Hapus pengajuan?', 'Pengajuan yang dihapus tidak dapat dikembalikan.')
            ->confirm(method: 'delete')
            ->cancel()
            ->send();
    }

    public function delete(): void
    {
        $this->reimbursement->delete();
        $this->dispatch('deleted');
        $this->success('Pengajuan berhasil dihapus');
    }
}
