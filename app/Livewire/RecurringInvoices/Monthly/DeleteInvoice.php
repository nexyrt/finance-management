<?php

namespace App\Livewire\RecurringInvoices\Monthly;

use TallStackUi\Traits\Interactions;
use App\Models\RecurringInvoice;
use Livewire\Component;
use Livewire\Attributes\Renderless;

class DeleteInvoice extends Component
{
    use Interactions;

    public RecurringInvoice $invoice;

    public function render(): string
    {
        return <<<'HTML'
        <div>
            <x-button.circle icon="trash" color="red" size="sm" wire:click="confirm" outline />
        </div>
        HTML;
    }

    #[Renderless]
    public function confirm(): void
    {
        if ($this->invoice->status === 'published') {
            $this->dialog()
                ->warning('Tidak Dapat Dihapus', 'Invoice yang sudah dipublish tidak bisa dihapus')
                ->send();
            return;
        }

        $this->dialog()
            ->question('Hapus Invoice?', 'Draft invoice ini akan dihapus permanen')
            ->confirm(method: 'delete')
            ->cancel()
            ->send();
    }

    public function delete(): void
    {
        $this->invoice->delete();
        $this->dispatch('invoice-deleted');
        $this->toast()->success('Success', 'Invoice deleted successfully')->send();
    }
}