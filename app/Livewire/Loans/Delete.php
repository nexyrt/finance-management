<?php

namespace App\Livewire\Loans;

use App\Livewire\Traits\Alert;
use App\Models\Loan;
use Livewire\Attributes\Renderless;
use Livewire\Component;

class Delete extends Component
{
    use Alert;

    public Loan $loan;

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
        $this->question("Hapus pinjaman {$this->loan->loan_number}?", "Data pembayaran juga akan terhapus.")
            ->confirm(method: 'delete')
            ->cancel()
            ->send();
    }

    public function delete(): void
    {
        // Check if has payments
        if ($this->loan->payments()->count() > 0) {
            $this->error('Tidak dapat menghapus pinjaman yang sudah ada pembayaran');
            return;
        }

        $this->loan->delete();

        $this->dispatch('deleted');
        $this->success('Pinjaman berhasil dihapus');
    }
}