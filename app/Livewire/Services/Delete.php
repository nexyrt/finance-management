<?php

namespace App\Livewire\Services;

use TallStackUi\Traits\Interactions;
use App\Models\Service;
use Livewire\Attributes\Renderless;
use Livewire\Component;

class Delete extends Component
{
    use Interactions;

    public Service $service;

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
        $this->dialog()->question(
            "Hapus Layanan?",
            "Layanan \"{$this->service->name}\" akan dihapus permanen"
        )
            ->confirm(method: 'delete')
            ->cancel()
            ->send();
    }

    // Step 2: Execute delete
    public function delete(): void
    {
        $this->service->delete();

        $this->dispatch('service-deleted');
        $this->dialog()->success('Berhasil', 'Layanan berhasil dihapus')->send();
    }
}
