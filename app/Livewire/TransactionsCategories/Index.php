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

    public array $headers = [
        ['index' => 'type', 'label' => 'Type'],
        ['index' => 'code', 'label' => 'Code'],
        ['index' => 'label', 'label' => 'Label'],
        ['index' => 'parent', 'label' => 'Parent', 'sortable' => false],
        ['index' => 'usage', 'label' => 'Usage', 'sortable' => false],
        ['index' => 'action', 'sortable' => false],
    ];

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
                $query->where(function ($q) {
                    $q->where('label', 'like', "%{$this->search}%")
                        ->orWhere('code', 'like', "%{$this->search}%");
                })
            )
            ->when($this->typeFilter, fn(Builder $query) =>
                $query->where('type', $this->typeFilter)
            )
            ->when(
                $this->sort['column'] === 'parent',
                fn(Builder $query) =>
                $query->leftJoin('transaction_categories as parents', 'transaction_categories.parent_code', '=', 'parents.code')
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
            'parents' => $categories->whereNull('parent_code')->count(),
            'children' => $categories->whereNotNull('parent_code')->count(),
            'with_transactions' => $categories->where('transactions_count', '>', 0)->count(),
        ];
    }
}