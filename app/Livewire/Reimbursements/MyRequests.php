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
use Livewire\Attributes\Renderless;
use Livewire\Component;
use Livewire\WithPagination;

class MyRequests extends Component
{
    use Alert, WithPagination;

    // Filter & Sorting - NO TYPE DECLARATION
    public $quantity = 10;
    public $search = null;
    public array $sort = ['column' => 'created_at', 'direction' => 'desc'];

    // Filters
    public $statusFilter = null;
    public $categoryFilter = null;
    public $dateRange = [];

    // Bulk Actions
    public array $selected = [];

    // Image Preview
    public bool $modal = false;
    public $previewImage = null;
    public $previewName = null;

    public function render(): View
    {
        return view('livewire.reimbursements.my-requests');
    }

    #[Computed]
    public function headers(): array
    {
        return [
            ['index' => 'request', 'label' => 'Request Information'],
            ['index' => 'amount', 'label' => 'Amount'],
            ['index' => 'status', 'label' => 'Status'],
            ['index' => 'actions', 'sortable' => false],
        ];
    }

    /**
     * Shared base query — avoids duplicating WHERE clauses between rows() and stats().
     */
    private function getFilteredQuery(): Builder
    {
        return Reimbursement::where('user_id', auth()->id())
            ->when(
                $this->search,
                fn(Builder $q) => $q->whereAny(['title', 'description', 'category_input'], 'like', '%' . trim($this->search) . '%')
            )
            ->when($this->statusFilter, fn(Builder $q) => $q->where('status', $this->statusFilter))
            ->when($this->categoryFilter, fn(Builder $q) => $q->where('category_input', $this->categoryFilter))
            ->when(
                !empty($this->dateRange) && count($this->dateRange) === 2,
                fn(Builder $q) => $q->whereBetween('expense_date', $this->dateRange)
            );
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        return $this->getFilteredQuery()
            ->with(['reviewer'])
            ->withSum('payments', 'amount')
            ->orderBy($this->sort['column'], $this->sort['direction'])
            ->paginate($this->quantity)
            ->withQueryString();
    }

    /**
     * Stats summary — single CASE WHEN query.
     */
    #[Computed]
    public function stats(): array
    {
        $result = $this->getFilteredQuery()
            ->selectRaw("
                COUNT(*) as total,
                SUM(amount) as total_amount,
                SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft_count,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count,
                SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_count,
                SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) as paid_count,
                SUM(amount_paid) as total_paid
            ")
            ->first();

        return [
            'total'         => (int) ($result->total ?? 0),
            'total_amount'  => (int) ($result->total_amount ?? 0),
            'draft_count'   => (int) ($result->draft_count ?? 0),
            'pending_count' => (int) ($result->pending_count ?? 0),
            'approved_count'=> (int) ($result->approved_count ?? 0),
            'paid_count'    => (int) ($result->paid_count ?? 0),
            'total_paid'    => (int) ($result->total_paid ?? 0),
        ];
    }

    #[Computed]
    public function statusOptions(): array
    {
        return [
            ['label' => 'Draft', 'value' => 'draft'],
            ['label' => 'Pending Review', 'value' => 'pending'],
            ['label' => 'Approved', 'value' => 'approved'],
            ['label' => 'Rejected', 'value' => 'rejected'],
            ['label' => 'Paid', 'value' => 'paid'],
        ];
    }

    #[Computed]
    public function categoryOptions(): array
    {
        return [
            ['label' => 'Transport', 'value' => 'transport'],
            ['label' => 'Meals & Entertainment', 'value' => 'meals'],
            ['label' => 'Office Supplies', 'value' => 'office_supplies'],
            ['label' => 'Communication', 'value' => 'communication'],
            ['label' => 'Accommodation', 'value' => 'accommodation'],
            ['label' => 'Medical', 'value' => 'medical'],
            ['label' => 'Other', 'value' => 'other'],
        ];
    }

    public function clearFilters(): void
    {
        $this->reset(['statusFilter', 'categoryFilter', 'dateRange', 'search']);
        $this->resetPage();
    }

    public function submitRequest(int $id): void
    {
        $reimbursement = Reimbursement::findOrFail($id);

        if ($reimbursement->user_id !== auth()->id()) {
            $this->error('Unauthorized action');
            return;
        }

        if (!$reimbursement->canSubmit()) {
            $this->error('Cannot submit this reimbursement');
            return;
        }

        $reimbursement->submit();
        $this->success('Reimbursement submitted for approval');
    }

    #[Renderless]
    public function confirmBulkDelete(): void
    {
        if (empty($this->selected)) {
            return;
        }

        $count = count($this->selected);
        $this->question("Delete {$count} reimbursements?", 'This action cannot be undone.')
            ->confirm(method: 'bulkDelete')
            ->cancel()
            ->send();
    }

    public function bulkDelete(): void
    {
        if (empty($this->selected)) {
            return;
        }

        $reimbursements = Reimbursement::whereIn('id', $this->selected)
            ->where('user_id', auth()->id())
            ->where('status', 'draft')
            ->get();

        $count = $reimbursements->count();

        if ($count === 0) {
            $this->error('No deletable reimbursements selected');
            return;
        }

        foreach ($reimbursements as $reimbursement) {
            $reimbursement->delete();
        }

        $this->selected = [];
        $this->resetPage();
        $this->success("{$count} reimbursements deleted successfully");
    }

    public function previewAttachment(int $id): void
    {
        $reimbursement = Reimbursement::findOrFail($id);

        if ($reimbursement->user_id !== auth()->id()) {
            $this->error('Unauthorized action');
            return;
        }

        if (!$reimbursement->hasAttachment() || !$reimbursement->isImageAttachment()) {
            return;
        }

        $this->previewImage = $reimbursement->attachment_url;
        $this->previewName = $reimbursement->attachment_name;
        $this->modal = true;
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