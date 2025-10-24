<?php

namespace App\Livewire\Users;

use App\Livewire\Traits\Alert;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Renderless;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination, Alert;

    public ?int $quantity = 10;
    public ?string $search = null;
    public array $sort = ['column' => 'created_at', 'direction' => 'desc'];
    public array $selected = [];

    public array $headers = [
        ['index' => 'name', 'label' => 'Name'],
        ['index' => 'email', 'label' => 'Email'],
        ['index' => 'role', 'label' => 'Role', 'sortable' => false],
        ['index' => 'status', 'label' => 'Status'],
        ['index' => 'created_at', 'label' => 'Joined'],
        ['index' => 'action', 'sortable' => false],
    ];

    public function render(): View
    {
        return view('livewire.users.index');
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        return User::with('roles')
            ->when($this->search, fn (Builder $query) => 
                $query->whereAny(['name', 'email', 'phone_number'], 'like', '%'.trim($this->search).'%')
            )
            ->orderBy($this->sort['column'], $this->sort['direction'])
            ->paginate($this->quantity)
            ->withQueryString();
    }

    #[Renderless]
    public function confirmBulkDelete(): void
    {
        if (empty($this->selected)) return;

        $count = count($this->selected);
        $this->question("Delete {$count} users?", "This action cannot be undone.")
            ->confirm(method: 'bulkDelete')
            ->cancel()
            ->send();
    }

    public function bulkDelete(): void
    {
        if (empty($this->selected)) return;

        // Prevent deleting current user
        $this->selected = array_diff($this->selected, [auth()->id()]);
        
        $count = count($this->selected);
        User::whereIn('id', $this->selected)->delete();
        
        $this->selected = [];
        $this->resetPage();
        $this->success("{$count} users deleted successfully");
    }
}