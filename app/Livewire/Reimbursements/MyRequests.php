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
    public $selected = [];

    public function render(): View
    {
        return view('livewire.reimbursements.my-requests');
    }

    #[Computed]
    public function headers(): array
    {
        return [
            ['index' => 'title', 'label' => 'Title'],
            ['index' => 'amount', 'label' => 'Amount'],
            ['index' => 'category', 'label' => 'Category'],
            ['index' => 'expense_date', 'label' => 'Date'],
            ['index' => 'status', 'label' => 'Status'],
            ['index' => 'payment_status', 'label' => 'Payment'],
            ['index' => 'action', 'sortable' => false],
        ];
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        return Reimbursement::with(['user', 'reviewer', 'payments.payer', 'payments.bankTransaction.bankAccount'])
            ->where('user_id', auth()->id())
            ->when($this->search, fn(Builder $query) => $query->whereAny(['title', 'description', 'category'], 'like', '%' . trim($this->search) . '%'))
            ->when($this->statusFilter, fn(Builder $query) => $query->where('status', $this->statusFilter))
            ->when($this->categoryFilter, fn(Builder $query) => $query->where('category', $this->categoryFilter))
            ->when(!empty($this->dateRange) && count($this->dateRange) === 2, fn(Builder $query) => $query->whereBetween('expense_date', $this->dateRange))
            ->orderBy($this->sort['column'], $this->sort['direction'])
            ->paginate($this->quantity)
            ->withQueryString();
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