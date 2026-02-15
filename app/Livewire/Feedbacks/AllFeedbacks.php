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

class AllFeedbacks extends Component
{
    use Interactions, WithPagination;

    public ?string $search = null;
    public ?string $statusFilter = null;
    public ?string $typeFilter = null;
    public ?string $priorityFilter = null;
    public int $quantity = 10;
    public array $sort = ['column' => 'created_at', 'direction' => 'desc'];

    public array $headers = [];

    public function mount(): void
    {
        $this->headers = [
            ['index' => 'type', 'label' => __('feedback.header_type'), 'sortable' => false],
            ['index' => 'title', 'label' => __('feedback.header_title')],
            ['index' => 'user', 'label' => __('feedback.header_sender'), 'sortable' => false],
            ['index' => 'priority', 'label' => __('feedback.header_priority'), 'sortable' => false],
            ['index' => 'status', 'label' => __('feedback.header_status'), 'sortable' => false],
            ['index' => 'created_at', 'label' => __('feedback.header_created')],
            ['index' => 'actions', 'label' => __('feedback.header_actions'), 'sortable' => false],
        ];
    }

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
        return view('livewire.feedbacks.all-feedbacks');
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        return Feedback::query()
            ->with('user')
            ->when($this->search, fn(Builder $q) => $q->where(function ($query) {
                $query->where('title', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%")
                    ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$this->search}%"));
            }))
            ->when($this->statusFilter, fn(Builder $q) => $q->byStatus($this->statusFilter))
            ->when($this->typeFilter, fn(Builder $q) => $q->byType($this->typeFilter))
            ->when($this->priorityFilter, fn(Builder $q) => $q->byPriority($this->priorityFilter))
            ->orderBy($this->sort['column'], $this->sort['direction'])
            ->paginate($this->quantity)
            ->withQueryString();
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

    public function updatedPriorityFilter(): void
    {
        $this->resetPage();
    }

    public function showFeedback(int $id): void
    {
        $this->dispatch('show::feedback', id: $id);
    }

    public function respondFeedback(int $id): void
    {
        $this->dispatch('respond::feedback', id: $id);
    }

    public function deleteFeedback(int $id): void
    {
        $this->dispatch('delete::feedback', id: $id);
    }

    public function changeStatus(int $id, string $status): void
    {
        $feedback = Feedback::find($id);

        if (!$feedback) {
            $this->toast()->error(__('common.error'), __('feedback.not_found'))->send();
            return;
        }

        $feedback->changeStatus($status);
        $this->toast()->success(__('common.success'), __('feedback.status_changed', ['status' => $status]))->send();
        $this->dispatch('feedback-updated');
    }
}
