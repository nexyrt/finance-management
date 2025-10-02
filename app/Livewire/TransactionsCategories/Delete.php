<?php

namespace App\Livewire\TransactionsCategories;

use App\Livewire\Traits\Alert;
use App\Models\TransactionCategory;
use Livewire\Attributes\Renderless;
use Livewire\Component;

class Delete extends Component
{
    use Alert;

    public TransactionCategory $category;

    public function render(): string
    {
        return <<<'HTML'
        <div>
            <x-button.circle icon="trash" color="red" size="sm" wire:click="confirm" title="Delete" />
        </div>
        HTML;
    }

    #[Renderless]
    public function confirm(): void
    {
        // Validation: check transactions
        $transactionsCount = $this->category->transactions()->count();
        if ($transactionsCount > 0) {
            $this->error('Cannot delete: category has ' . $transactionsCount . ' transactions');
            return;
        }

        // Validation: check children
        $childrenCount = $this->category->children()->count();
        if ($childrenCount > 0) {
            $this->error('Cannot delete: category has ' . $childrenCount . ' child categories');
            return;
        }

        $this->question('Delete category?', 'This action cannot be undone.')
            ->confirm(method: 'delete')
            ->cancel()
            ->send();
    }

    public function delete(): void
    {
        $this->category->delete();
        $this->dispatch('deleted');
        $this->success('Category deleted successfully');
    }
}