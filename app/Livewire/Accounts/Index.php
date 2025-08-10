<?php

namespace App\Livewire\Accounts;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    // Selected account for main content
    public $selectedAccountId = null;

    // Search and filters for main content
    public $search = '';
    public $transactionType = '';
    public $dateRange = [];

    // Stats cache
    public $accountsData = [];
    public $totalBalance = 0;
    public $totalIncome = 0;
    public $totalExpense = 0;

    public function mount()
    {
        $this->calculateStats();
        
        // Auto-select first account if available
        if ($this->accountsData->count() > 0) {
            $this->selectedAccountId = $this->accountsData->first()['id'];
        }
    }

    public function selectAccount($accountId = null)
    {
        $this->selectedAccountId = $accountId;
        $this->resetPage();
    }

    public function refreshData()
    {
        $this->resetPage();
        $this->calculateStats();
    }

    private function calculateStats()
    {
        $this->accountsData = BankAccount::with(['transactions' => function($query) {
            $query->latest()->take(3);
        }])->get()->map(function($account) {
            return [
                'id' => $account->id,
                'name' => $account->account_name,
                'bank' => $account->bank_name,
                'account_number' => $account->account_number,
                'balance' => $account->balance,
                'recent_transactions' => $account->transactions,
                'trend' => $this->calculateTrend($account->id)
            ];
        });

        $this->totalBalance = $this->accountsData->sum('balance');
        $this->totalIncome = BankTransaction::where('transaction_type', 'credit')->sum('amount');
        $this->totalExpense = BankTransaction::where('transaction_type', 'debit')->sum('amount');
    }

    private function calculateTrend($accountId)
    {
        $thisMonth = BankTransaction::where('bank_account_id', $accountId)
            ->where('transaction_type', 'credit')
            ->whereMonth('transaction_date', now()->month)
            ->sum('amount');
        $lastMonth = BankTransaction::where('bank_account_id', $accountId)
            ->where('transaction_type', 'credit')
            ->whereMonth('transaction_date', now()->subMonth()->month)
            ->sum('amount');
        
        return $thisMonth >= $lastMonth ? 'up' : 'down';
    }

    public function with(): array
    {
        return [
            'transactions' => $this->getTransactions(),
            'cashflowData' => $this->getCashflowData(),
        ];
    }

    private function getTransactions()
    {
        $query = BankTransaction::with('bankAccount')
            ->when($this->selectedAccountId, fn($q) => $q->where('bank_account_id', $this->selectedAccountId))
            ->when($this->search, function($q) {
                $q->where(function($query) {
                    $query->where('description', 'like', "%{$this->search}%")
                          ->orWhere('reference_number', 'like', "%{$this->search}%");
                });
            })
            ->when($this->transactionType, fn($q) => $q->where('transaction_type', $this->transactionType))
            ->when(!empty($this->dateRange) && count($this->dateRange) >= 2, function($q) {
                $q->whereBetween('transaction_date', $this->dateRange);
            });

        return $query->latest('transaction_date')->paginate(10);
    }

    private function getCashflowData()
    {
        return collect(range(5, 0))->map(function($monthsBack) {
            $date = now()->subMonths($monthsBack);
            $accountFilter = $this->selectedAccountId ? ['bank_account_id' => $this->selectedAccountId] : [];
            
            return [
                'month' => $date->format('M'),
                'income' => BankTransaction::where('transaction_type', 'credit')
                    ->where($accountFilter)
                    ->whereMonth('transaction_date', $date->month)
                    ->whereYear('transaction_date', $date->year)
                    ->sum('amount'),
                'expense' => BankTransaction::where('transaction_type', 'debit')
                    ->where($accountFilter)
                    ->whereMonth('transaction_date', $date->month)
                    ->whereYear('transaction_date', $date->year)
                    ->sum('amount')
            ];
        });
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->transactionType = '';
        $this->dateRange = [];
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.accounts.index', $this->with());
    }
}