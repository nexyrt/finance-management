<?php

namespace App\Livewire\Reimbursements;

use App\Livewire\Traits\Alert;
use App\Models\Reimbursement;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
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
            ['index' => 'attachment', 'label' => '', 'sortable' => false],
            ['index' => 'title', 'label' => 'Title'],
            ['index' => 'category', 'label' => 'Category'],
            ['index' => 'expense_date', 'label' => 'Date'],
            ['index' => 'amount', 'label' => 'Amount'],
            ['index' => 'status', 'label' => 'Status'],
            ['index' => 'action', 'sortable' => false],
        ];
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        return Reimbursement::with(['reviewer'])
            ->where('user_id', auth()->id())
            ->when(
                $this->search,
                fn(Builder $query) =>
                $query->whereAny(['title', 'description', 'category_input'], 'like', '%' . trim($this->search) . '%')
            )
            ->when(
                $this->statusFilter,
                fn(Builder $query) =>
                $query->where('status', $this->statusFilter)
            )
            ->when(
                $this->categoryFilter,
                fn(Builder $query) =>
                $query->where('category_input', $this->categoryFilter)
            )
            ->when(
                !empty($this->dateRange) && count($this->dateRange) === 2,
                fn(Builder $query) =>
                $query->whereBetween('expense_date', $this->dateRange)
            )
            ->withSum('payments', 'amount')
            ->orderBy($this->sort['column'], $this->sort['direction'])
            ->paginate($this->quantity)
            ->withQueryString();
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
        unset($this->rows);
    }
}