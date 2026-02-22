<?php

namespace App\Livewire\Users;

use App\Livewire\Traits\Alert;
use App\Models\User;
use Livewire\Attributes\Renderless;
use Livewire\Component;

class Delete extends Component
{
    use Alert;

    public User $user;

    public function render(): string
    {
        return <<<'HTML'
        <div>
            <x-button.circle icon="trash" color="red" wire:click="confirm" size="sm" />
        </div>
        HTML;
    }

    #[Renderless]
    public function confirm(): void
    {
        $this->authorize('manage users');
        
        // Prevent deleting current user
        if ($this->user->id === auth()->id()) {
            $this->error(__('pages.user_cannot_delete_self'));
            return;
        }

        $this->question(__('pages.user_delete_confirm', ['name' => $this->user->name]), __('pages.user_cannot_undo'))
            ->confirm(method: 'delete')
            ->cancel()
            ->send();
    }

    public function delete(): void
    {
        $this->authorize('manage users');
        
        $this->user->delete();
        $this->dispatch('deleted');
        $this->success(__('pages.user_deleted'));
    }
}