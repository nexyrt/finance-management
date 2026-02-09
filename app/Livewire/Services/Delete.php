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
            __('pages.delete_service'),
            __('pages.confirm_delete_service', ['name' => $this->service->name])
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
        $this->dialog()->success(__('common.success'), __('common.deleted_successfully'))->send();
    }
}
