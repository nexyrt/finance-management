<?php

namespace App\Livewire\Users;

use App\Livewire\Traits\Alert;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
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

    public array $headers = [];

    public function mount(): void
    {
        $this->headers = [
            ['index' => 'name', 'label' => __('pages.user_col_name')],
            ['index' => 'email', 'label' => __('pages.user_col_email')],
            ['index' => 'role', 'label' => __('pages.user_col_role'), 'sortable' => false],
            ['index' => 'status', 'label' => __('pages.user_col_status')],
            ['index' => 'created_at', 'label' => __('pages.user_col_joined')],
            ['index' => 'action', 'sortable' => false],
        ];
    }

    #[On('created')]
    #[On('updated')]
    #[On('deleted')]
    public function refreshStats(): void
    {
        unset($this->stats);
        $this->reset('selected');
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.users.index');
    }

    #[Computed]
    public function stats(): array
    {
        $total = User::count();
        $active = User::where('status', 'active')->count();
        $admins = User::role('admin')->count();
        $financeManagers = User::role('finance manager')->count();

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $total - $active,
            'admins' => $admins,
            'finance_managers' => $financeManagers,
        ];
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
        $this->question(__('pages.user_bulk_delete', ['count' => $count]), __('pages.user_cannot_undo'))
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
        $this->success(__('pages.user_bulk_deleted', ['count' => $count]));
    }
}