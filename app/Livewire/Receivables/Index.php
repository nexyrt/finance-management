<?php

namespace App\Livewire\Receivables;

use App\Livewire\Traits\Alert;
use App\Models\Receivable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Component;
use Livewire\WithPagination;

#[Lazy]
class Index extends Component
{
    use WithPagination, Alert;

    public bool $guideModal = false;

    // Filters - NO TYPE DECLARATION
    public $quantity = 10;
    public $search = null;
    public array $sort = ['column' => 'created_at', 'direction' => 'desc'];

    public $statusFilter = null;
    public $typeFilter = null;

    // Bulk Actions
    public array $selected = [];

    // Headers populated in mount() so __() works
    public array $headers = [];

    public function mount(): void
    {
        $this->headers = [
            ['index' => 'receivable_number', 'label' => __('pages.col_receivable_number'), 'sortable' => false],
            ['index' => 'debtor', 'label' => __('pages.col_borrower'), 'sortable' => false],
            ['index' => 'principal_amount', 'label' => __('pages.col_principal')],
            ['index' => 'interest_amount', 'label' => __('pages.col_interest'), 'sortable' => false],
            ['index' => 'installment_months', 'label' => __('pages.col_tenor'), 'sortable' => false],
            ['index' => 'loan_date', 'label' => __('pages.col_loan_date')],
            ['index' => 'status', 'label' => __('common.status')],
            ['index' => 'action', 'sortable' => false],
        ];
    }

    public function placeholder(): View
    {
        return view('livewire.placeholders.table-skeleton');
    }

    public function render(): View
    {
        return view('livewire.receivables.index');
    }

    #[Computed]
    public function stats(): array
    {
        $base = Receivable::query();

        return [
            'total'                  => (clone $base)->count(),
            'active'                 => (clone $base)->where('status', 'active')->count(),
            'pending'                => (clone $base)->where('status', 'pending_approval')->count(),
            'total_principal_active' => (clone $base)->where('status', 'active')->sum('principal_amount'),
        ];
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        return Receivable::with(['debtor'])
            ->withSum('payments', 'principal_paid')
            ->withSum('payments', 'interest_paid')
            ->when(
                $this->search,
                fn(Builder $query) =>
                $query->where('receivable_number', 'like', '%' . trim($this->search) . '%')
                    ->orWhere('purpose', 'like', '%' . trim($this->search) . '%')
            )
            ->when(
                $this->statusFilter,
                fn(Builder $query) =>
                $query->where('status', $this->statusFilter)
            )
            ->when(
                $this->typeFilter,
                fn(Builder $query) =>
                $query->where('type', $this->typeFilter)
            )
            ->orderBy($this->sort['column'], $this->sort['direction'])
            ->paginate($this->quantity)
            ->withQueryString();
    }

    public function submitReceivable($id): void
    {
        $receivable = Receivable::findOrFail($id);

        if ($receivable->status !== 'draft') {
            $this->error(__('pages.rcv_only_draft_submittable'));
            return;
        }

        $receivable->update(['status' => 'pending_approval']);
        $this->success(__('pages.rcv_submitted_success'));
    }

    #[Computed]
    public function statusOptions(): array
    {
        return [
            ['label' => __('pages.rcv_status_draft'), 'value' => 'draft'],
            ['label' => __('pages.rcv_status_pending'), 'value' => 'pending_approval'],
            ['label' => __('pages.rcv_status_active'), 'value' => 'active'],
            ['label' => __('pages.rcv_status_paid_off'), 'value' => 'paid_off'],
            ['label' => __('pages.rcv_status_rejected'), 'value' => 'rejected'],
        ];
    }

    #[Computed]
    public function typeOptions(): array
    {
        return [
            ['label' => __('pages.rcv_type_employee'), 'value' => 'employee_loan'],
            ['label' => __('pages.rcv_type_company'), 'value' => 'company_loan'],
        ];
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['statusFilter', 'typeFilter', 'search']);
        $this->resetPage();
    }
}
