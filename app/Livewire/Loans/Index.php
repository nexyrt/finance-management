<?php

namespace App\Livewire\Loans;

use App\Livewire\Traits\Alert;
use App\Models\Loan;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination, Alert;

    // Filters - NO TYPE DECLARATION
    public $quantity = 10;
    public $search = null;
    public $statusFilter = null;
    public array $sort = ['column' => 'start_date', 'direction' => 'desc'];


    public array $headers = [
        ['index' => 'loan_number', 'label' => 'Pinjaman', 'sortable' => false],
        ['index' => 'principal_amount', 'label' => 'Pokok'],
        ['index' => 'interest_amount', 'label' => 'Bunga', 'sortable' => false],
        ['index' => 'term_months', 'label' => 'Tenor', 'sortable' => false],
        ['index' => 'start_date', 'label' => 'Tanggal'],
        ['index' => 'status', 'label' => 'Status'],
        ['index' => 'action', 'sortable' => false],
    ];

    public function render(): View
    {
        return view('livewire.loans.index');
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        return Loan::query()
            ->when(
                $this->search,
                fn(Builder $query) =>
                $query->where('loan_number', 'like', '%' . trim($this->search) . '%')
                    ->orWhere('lender_name', 'like', '%' . trim($this->search) . '%')
            )
            ->when(
                $this->statusFilter,
                fn(Builder $query) =>
                $query->where('status', $this->statusFilter)
            )
            ->orderBy($this->sort['column'], $this->sort['direction'])
            ->paginate($this->quantity)
            ->withQueryString();
    }

    #[Computed]
    public function statusOptions(): array
    {
        return [
            ['label' => 'Aktif', 'value' => 'active'],
            ['label' => 'Lunas', 'value' => 'paid_off'],
        ];
    }

    public function clearFilters(): void
    {
        $this->reset(['statusFilter', 'search']);
        $this->resetPage();
    }
}