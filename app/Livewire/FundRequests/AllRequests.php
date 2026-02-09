<?php

namespace App\Livewire\FundRequests;

use App\Livewire\Traits\Alert;
use App\Models\FundRequest;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class AllRequests extends Component
{
    use Alert, WithPagination, Interactions;

    public string $search = '';
    public string $statusFilter = '';
    public string $priorityFilter = '';
    public string $userFilter = '';
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

    public function updatingUserFilter(): void
    {
        $this->resetPage();
    }

    #[On('fund-request-created')]
    #[On('fund-request-updated')]
    #[On('fund-request-deleted')]
    #[On('fund-request-submitted')]
    #[On('fund-request-reviewed')]
    #[On('fund-request-disbursed')]
    public function refresh(): void
    {
        $this->resetPage();
        unset($this->rows);
        unset($this->users);
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
        $query = FundRequest::with(['user', 'reviewer', 'disburser'])
            ->withCount('items');

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                    ->orWhere('purpose', 'like', "%{$this->search}%")
                    ->orWhereHas('user', function ($q) {
                        $q->where('name', 'like', "%{$this->search}%");
                    });
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

        // User filter
        if ($this->userFilter) {
            $query->where('user_id', $this->userFilter);
        }

        // Sort
        $query->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate(10);
    }

    #[Computed]
    public function users()
    {
        return \App\Models\User::orderBy('name')->get();
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
        if ($this->userFilter) {
            $count++;
        }

        return $count;
    }

    public function clearFilters(): void
    {
        $this->reset(['statusFilter', 'priorityFilter', 'userFilter', 'search']);
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.fund-requests.all-requests', [
            'headers' => [
                ['index' => 'user', 'label' => 'Requestor'],
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
