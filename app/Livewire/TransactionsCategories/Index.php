<?php

namespace App\Livewire\TransactionsCategories;

use App\Livewire\Traits\Alert;
use App\Models\TransactionCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination, Alert;

    public ?int $quantity = 10;
    public ?string $search = null;
    public ?string $typeFilter = null;
    public array $sort = ['column' => 'type', 'direction' => 'asc'];

    public array $headers = [];

    public function mount(): void
    {
        $this->headers = [
            ['index' => 'type', 'label' => __('pages.cat_col_type')],
            ['index' => 'label', 'label' => __('pages.cat_col_label')],
            ['index' => 'parent', 'label' => __('pages.cat_col_parent'), 'sortable' => false],
            ['index' => 'usage', 'label' => __('pages.cat_col_usage'), 'sortable' => false],
            ['index' => 'action', 'sortable' => false],
        ];
    }

    public function render(): View
    {
        return view('livewire.transactions-categories.index');
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        return TransactionCategory::with(['parent', 'children'])
            ->withCount(['transactions', 'children'])
            ->when($this->search, fn(Builder $query) =>
                $query->where('label', 'like', "%{$this->search}%")
            )
            ->when($this->typeFilter, fn(Builder $query) =>
                $query->where('type', $this->typeFilter)
            )
            ->when(
                $this->sort['column'] === 'parent',
                fn(Builder $query) =>
                $query->leftJoin('transaction_categories as parents', 'transaction_categories.parent_id', '=', 'parents.id')
                    ->orderBy('parents.label', $this->sort['direction'])
                    ->select('transaction_categories.*'),
                fn(Builder $query) =>
                $query->orderBy($this->sort['column'], $this->sort['direction'])
            )
            ->paginate($this->quantity)
            ->withQueryString();
    }

    #[Computed]
    public function stats(): array
    {
        $categories = TransactionCategory::withCount('transactions')->get();

        return [
            'total' => $categories->count(),
            'parents' => $categories->whereNull('parent_id')->count(),
            'children' => $categories->whereNotNull('parent_id')->count(),
            'with_transactions' => $categories->where('transactions_count', '>', 0)->count(),
        ];
    }
}