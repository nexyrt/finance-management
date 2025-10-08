<?php

namespace App\Livewire\CashFlow;

use App\Models\BankTransaction;
use App\Models\BankAccount;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Illuminate\Support\Collection;

class TransfersTab extends Component
{
    use WithPagination;

    public ?string $startDate = null;
    public ?string $endDate = null;
    public ?int $fromAccountId = null;
    public ?int $toAccountId = null;
    public ?string $search = null;

    public function mount(): void
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
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
    public function transferTransactions(): Collection
    {
        return BankTransaction::with(['bankAccount', 'category'])
            ->whereHas('category', function ($query) {
                $query->where('type', 'transfer');
            })
            ->when($this->startDate, fn($q) => $q->whereDate('transaction_date', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('transaction_date', '<=', $this->endDate))
            ->when($this->fromAccountId, fn($q) => $q->where(function($query) {
                $query->where('bank_account_id', $this->fromAccountId)
                    ->where('transaction_type', 'debit');
            }))
            ->when($this->toAccountId, fn($q) => $q->where(function($query) {
                $query->where('bank_account_id', $this->toAccountId)
                    ->where('transaction_type', 'credit');
            }))
            ->when($this->search, fn($q) => $q->where(function($query) {
                $query->where('description', 'like', "%{$this->search}%")
                    ->orWhere('reference_number', 'like', "%{$this->search}%");
            }))
            ->orderBy('transaction_date', 'desc')
            ->get();
    }

    #[Computed]
    public function totalTransfers(): int
    {
        return $this->transferTransactions->sum('amount');
    }

    #[Computed]
    public function transfersByAccount(): Collection
    {
        return $this->transferTransactions
            ->groupBy(fn($t) => $t->bankAccount->account_name)
            ->map(fn($transactions, $accountName) => [
                'account' => $accountName,
                'debits' => $transactions->where('transaction_type', 'debit')->sum('amount'),
                'credits' => $transactions->where('transaction_type', 'credit')->sum('amount'),
                'count' => $transactions->count(),
            ])
            ->values();
    }

    public function applyFilters(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['startDate', 'endDate', 'fromAccountId', 'toAccountId', 'search']);
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.cash-flow.transfers-tab');
    }
}