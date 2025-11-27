<?php

namespace App\Livewire\Receivables;

use App\Livewire\Traits\Alert;
use App\Models\Receivable;
use Livewire\Component;

class Delete extends Component
{
    use Alert;

    public Receivable $receivable;

    public function render(): string
    {
        return <<<'HTML'
        <div>
            <x-button.circle icon="trash" color="red" size="sm" wire:click="confirm" title="Hapus" />
        </div>
        HTML;
    }

    public function confirm(): void
    {
        // Validation: only draft can be deleted
        if ($this->receivable->status !== 'draft') {
            $this->error('Hanya piutang draft yang bisa dihapus');
            return;
        }

        $this->question("Hapus piutang {$this->receivable->receivable_number}?", "Tindakan ini tidak dapat dibatalkan.")
            ->confirm(method: 'delete')
            ->cancel()
            ->send();
    }

    public function delete(): void
    {
        // Safety check: cannot delete if has payments
        if ($this->receivable->payments()->exists()) {
            $this->error('Tidak bisa menghapus piutang yang sudah ada pembayaran');
            return;
        }

        // Safety check: only draft
        if ($this->receivable->status !== 'draft') {
            $this->error('Hanya piutang draft yang bisa dihapus');
            return;
        }

        $this->receivable->delete();

        $this->dispatch('deleted');
        $this->success('Piutang berhasil dihapus');
    }
}