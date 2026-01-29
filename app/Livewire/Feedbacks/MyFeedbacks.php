<?php

namespace App\Livewire\Feedbacks;

use App\Models\Feedback;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class MyFeedbacks extends Component
{
    use Interactions, WithPagination;

    public ?string $search = null;
    public ?string $statusFilter = null;
    public ?string $typeFilter = null;
    public int $quantity = 10;
    public array $sort = ['column' => 'created_at', 'direction' => 'desc'];

    public array $headers = [
        ['index' => 'type', 'label' => 'Jenis', 'sortable' => false],
        ['index' => 'title', 'label' => 'Judul'],
        ['index' => 'priority', 'label' => 'Prioritas', 'sortable' => false],
        ['index' => 'status', 'label' => 'Status', 'sortable' => false],
        ['index' => 'created_at', 'label' => 'Dibuat'],
        ['index' => 'actions', 'label' => 'Aksi', 'sortable' => false],
    ];

    #[On('feedback-created')]
    #[On('feedback-updated')]
    #[On('feedback-deleted')]
    #[On('feedback-responded')]
    public function refresh(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.feedbacks.my-feedbacks');
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        return Feedback::query()
            ->forUser(auth()->id())
            ->when($this->search, fn(Builder $q) => $q->where(function ($query) {
                $query->where('title', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%");
            }))
            ->when($this->statusFilter, fn(Builder $q) => $q->byStatus($this->statusFilter))
            ->when($this->typeFilter, fn(Builder $q) => $q->byType($this->typeFilter))
            ->orderBy($this->sort['column'], $this->sort['direction'])
            ->paginate($this->quantity)
            ->withQueryString();
    }

    #[Computed]
    public function totalCount(): int
    {
        return Feedback::query()->forUser(auth()->id())->count();
    }

    #[Computed]
    public function hasFilters(): bool
    {
        return !empty($this->search) || !is_null($this->statusFilter) || !is_null($this->typeFilter);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedTypeFilter(): void
    {
        $this->resetPage();
    }

    public function showFeedback(int $id): void
    {
        $this->dispatch('show::feedback', id: $id);
    }

    public function editFeedback(int $id): void
    {
        $this->dispatch('edit::feedback', id: $id);
    }

    public function deleteFeedback(int $id): void
    {
        $this->dispatch('delete::feedback', id: $id);
    }
}
