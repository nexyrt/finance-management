<?php

namespace App\Livewire\CashFlow;

use App\Models\BankTransaction;
use App\Models\BankAccount;
use App\Models\TransactionCategory;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Illuminate\Support\Collection;

class ExpensesTab extends Component
{
    use WithPagination;

    public ?string $startDate = null;
    public ?string $endDate = null;
    public ?int $categoryId = null;
    public ?int $bankAccountId = null;
    public ?string $search = null;

    public function mount(): void
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    #[Computed]
    public function categories(): array
    {
        return TransactionCategory::where('type', 'expense')
            ->orderBy('label')
            ->get()
            ->map(fn($cat) => ['label' => $cat->label, 'value' => $cat->id])
            ->toArray();
    }

    #[Computed]
    public function bankAccounts(): array
    {
        return BankAccount::orderBy('account_name')
            ->get()
            ->map(fn($acc) => ['label' => $acc->account_name, 'value' => $acc->id])
            ->toArray();
    }

    #[Computed]
    public function expenseTransactions(): Collection
    {
        return BankTransaction::with(['bankAccount', 'category'])
            ->where('transaction_type', 'debit')
            ->when($this->startDate, fn($q) => $q->whereDate('transaction_date', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('transaction_date', '<=', $this->endDate))
            ->when($this->categoryId, fn($q) => $q->where('category_id', $this->categoryId))
            ->when($this->bankAccountId, fn($q) => $q->where('bank_account_id', $this->bankAccountId))
            ->when($this->search, fn($q) => $q->where(function($query) {
                $query->where('description', 'like', "%{$this->search}%")
                    ->orWhere('reference_number', 'like', "%{$this->search}%");
            }))
            ->orderBy('transaction_date', 'desc')
            ->get();
    }

    #[Computed]
    public function totalExpenses(): int
    {
        return $this->expenseTransactions->sum('amount');
    }

    #[Computed]
    public function expensesByCategory(): Collection
    {
        return $this->expenseTransactions
            ->groupBy(fn($t) => $t->category?->label ?? 'Uncategorized')
            ->map(fn($transactions) => [
                'category' => $transactions->first()->category?->label ?? 'Uncategorized',
                'total' => $transactions->sum('amount'),
                'count' => $transactions->count(),
            ])
            ->sortByDesc('total')
            ->values();
    }

    public function applyFilters(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['startDate', 'endDate', 'categoryId', 'bankAccountId', 'search']);
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.cash-flow.expenses-tab');
    }
}