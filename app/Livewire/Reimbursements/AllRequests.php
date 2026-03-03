<?php

namespace App\Livewire\Reimbursements;

use App\Livewire\Traits\Alert;
use App\Models\Reimbursement;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class AllRequests extends Component
{
    use Alert, WithPagination;

    // Filter & Sorting
    public $quantity = 10;
    public $search = null;
    public array $sort = ['column' => 'created_at', 'direction' => 'desc'];

    // Filters
    public $statusFilter = null;
    public $categoryFilter = null;
    public $dateRange = [];

    // Bulk Actions
    public array $selected = [];

    public array $headers = [];

    public function mount(): void
    {
        $this->headers = [
            ['index' => 'title', 'label' => __('pages.reimb_col_title')],
            ['index' => 'user', 'label' => __('pages.reimb_col_requestor'), 'sortable' => true],
            ['index' => 'amount', 'label' => __('pages.reimb_col_amount')],
            ['index' => 'category', 'label' => __('common.category')],
            ['index' => 'expense_date', 'label' => __('common.date')],
            ['index' => 'status', 'label' => __('common.status')],
            ['index' => 'payment_status', 'label' => __('pages.reimb_col_payment')],
            ['index' => 'action', 'sortable' => false],
        ];
    }

    public function render(): View
    {
        return view('livewire.reimbursements.all-requests');
    }

    /**
     * Shared base query with all filters applied — avoids duplicating WHERE clauses.
     */
    private function getFilteredQuery(): Builder
    {
        return Reimbursement::query()
            ->when($this->search, function (Builder $query) {
                $search = '%' . trim($this->search) . '%';
                $query->where(function ($q) use ($search) {
                    $q->where('reimbursements.title', 'like', $search)
                        ->orWhere('reimbursements.description', 'like', $search)
                        ->orWhere('reimbursements.category_input', 'like', $search)
                        ->orWhereExists(function ($sub) use ($search) {
                            $sub->select(DB::raw(1))
                                ->from('users')
                                ->whereColumn('users.id', 'reimbursements.user_id')
                                ->where('users.name', 'like', $search);
                        });
                });
            })
            ->when($this->statusFilter, fn(Builder $q) => $q->where('reimbursements.status', $this->statusFilter))
            ->when($this->categoryFilter, fn(Builder $q) => $q->where('reimbursements.category_input', $this->categoryFilter))
            ->when(
                !empty($this->dateRange) && count($this->dateRange) === 2,
                fn(Builder $q) => $q->whereBetween('reimbursements.expense_date', $this->dateRange)
            );
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        return $this->getFilteredQuery()
            ->with(['user', 'reviewer', 'category', 'payments.payer', 'payments.bankTransaction.bankAccount'])
            ->when(
                $this->sort['column'] === 'user',
                fn(Builder $query) => $query->leftJoin('users', 'reimbursements.user_id', '=', 'users.id')
                    ->orderBy('users.name', $this->sort['direction'])
                    ->select('reimbursements.*'),
                fn(Builder $query) => $query->orderBy('reimbursements.' . $this->sort['column'], $this->sort['direction'])
            )
            ->paginate($this->quantity)
            ->withQueryString();
    }

    /**
     * Stats summary — single CASE WHEN query instead of multiple separate queries.
     */
    #[Computed]
    public function stats(): array
    {
        $result = $this->getFilteredQuery()
            ->selectRaw("
                COUNT(*) as total,
                SUM(amount) as total_amount,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count,
                SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_count,
                SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) as paid_count,
                SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_count,
                SUM(CASE WHEN status = 'pending' THEN amount ELSE 0 END) as pending_amount,
                SUM(CASE WHEN status IN ('approved','paid') THEN amount ELSE 0 END) as approved_amount,
                SUM(amount_paid) as total_paid
            ")
            ->first();

        return [
            'total'          => (int) ($result->total ?? 0),
            'total_amount'   => (int) ($result->total_amount ?? 0),
            'pending_count'  => (int) ($result->pending_count ?? 0),
            'approved_count' => (int) ($result->approved_count ?? 0),
            'paid_count'     => (int) ($result->paid_count ?? 0),
            'rejected_count' => (int) ($result->rejected_count ?? 0),
            'pending_amount' => (int) ($result->pending_amount ?? 0),
            'approved_amount'=> (int) ($result->approved_amount ?? 0),
            'total_paid'     => (int) ($result->total_paid ?? 0),
        ];
    }

    #[Computed]
    public function statusOptions(): array
    {
        return collect(Reimbursement::statuses())
            ->map(fn($status) => ['label' => $status['label'], 'value' => $status['value']])
            ->toArray();
    }

    #[Computed]
    public function categoryOptions(): array
    {
        return collect(Reimbursement::categories())
            ->map(fn($category) => ['label' => $category['label'], 'value' => $category['value']])
            ->toArray();
    }

    public function clearFilters(): void
    {
        $this->reset(['statusFilter', 'categoryFilter', 'dateRange', 'search']);
        $this->resetPage();
    }

    #[On('refreshed')]
    #[On('created')]
    #[On('updated')]
    #[On('deleted')]
    #[On('reviewed')]
    #[On('paid')]
    public function refresh(): void
    {
        unset($this->rows, $this->stats);
    }
}
