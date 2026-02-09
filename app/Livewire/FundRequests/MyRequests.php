<?php

namespace App\Livewire\FundRequests;

use App\Livewire\Traits\Alert;
use App\Models\FundRequest;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class MyRequests extends Component
{
    use Alert, WithPagination, Interactions;

    public string $search = '';
    public string $statusFilter = '';
    public string $priorityFilter = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';
    public array $selected = [];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingPriorityFilter(): void
    {
        $this->resetPage();
    }

    #[On('fund-request-created')]
    #[On('fund-request-updated')]
    #[On('fund-request-deleted')]
    #[On('fund-request-submitted')]
    public function refresh(): void
    {
        $this->resetPage();
        unset($this->rows);
    }

    public function sort(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    #[Computed]
    public function rows()
    {
        $query = FundRequest::with(['reviewer', 'disburser'])
            ->withCount('items')
            ->where('user_id', auth()->id());

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('purpose', 'like', '%' . $this->search . '%');
            });
        }

        // Status filter
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        // Priority filter
        if ($this->priorityFilter) {
            $query->where('priority', $this->priorityFilter);
        }

        // Sort
        $query->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate(10);
    }

    #[Computed]
    public function activeFilters(): int
    {
        $count = 0;
        if ($this->statusFilter) {
            $count++;
        }
        if ($this->priorityFilter) {
            $count++;
        }

        return $count;
    }

    public function clearFilters(): void
    {
        $this->reset(['statusFilter', 'priorityFilter', 'search']);
        $this->resetPage();
    }

    public function submitRequest(int $id): void
    {
        $this->dispatch('submit-request', id: $id);
    }

    public function confirmSubmit(int $id): void
    {
        $fundRequest = FundRequest::findOrFail($id);

        // Check authorization
        if ($fundRequest->user_id !== auth()->id()) {
            $this->toast()->error('Unauthorized', 'You cannot submit this request.')->send();

            return;
        }

        if (! $fundRequest->canSubmit()) {
            $this->toast()->error('Cannot Submit', 'This request cannot be submitted. Please add items first.')->send();

            return;
        }

        $fundRequest->submit();

        $this->toast()->success('Request Submitted', 'Your fund request has been submitted for review.')->send();
        $this->dispatch('fund-request-submitted');
    }

    public function render()
    {
        return view('livewire.fund-requests.my-requests', [
            'headers' => [
                ['index' => 'title', 'label' => 'Request Title', 'sortable' => true],
                ['index' => 'total_amount', 'label' => 'Amount'],
                ['index' => 'priority', 'label' => 'Priority'],
                ['index' => 'needed_by_date', 'label' => 'Needed By'],
                ['index' => 'status', 'label' => 'Status'],
                ['index' => 'created_at', 'label' => 'Created', 'sortable' => true],
                ['index' => 'actions', 'label' => 'Actions'],
            ],
            'sort' => [
                'column' => $this->sortField,
                'direction' => $this->sortDirection,
            ],
        ]);
    }
}
