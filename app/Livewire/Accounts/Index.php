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

    // Listeners for real-time updates
    protected $listeners = [
        'account-created' => 'refreshData',
        'account-updated' => 'refreshData', 
        'account-deleted' => 'handleAccountDeleted',
        'transaction-created' => 'refreshData',
        'refreshChart' => 'refreshChart'
    ];

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
        
        // Dispatch event to refresh chart with new account data
        $this->dispatch('account-selected', accountId: $accountId);
    }

    public function refreshData()
    {
        $this->resetPage();
        $this->calculateStats();
        
        // Refresh chart after data update
        $this->dispatch('chart-data-updated');
    }

    public function handleAccountDeleted($accountId)
    {
        // If deleted account was selected, reset selection
        if ($this->selectedAccountId == $accountId) {
            $this->selectedAccountId = null;
        }
        
        $this->refreshData();
        
        // Auto-select first available account
        if ($this->accountsData->count() > 0 && !$this->selectedAccountId) {
            $this->selectedAccountId = $this->accountsData->first()['id'];
            $this->dispatch('account-selected', accountId: $this->selectedAccountId);
        }
    }

    public function refreshChart()
    {
        // This method is called to trigger chart refresh
        $this->dispatch('chart-refresh-requested');
    }

    private function calculateStats()
    {
        $this->accountsData = BankAccount::with(['transactions' => function($query) {
            $query->latest()->take(3);
        }])->get()->map(function($account) {
            $balance = $this->calculateAccountBalance($account->id);
            return [
                'id' => $account->id,
                'name' => $account->account_name,
                'bank' => $account->bank_name,
                'account_number' => $account->account_number,
                'balance' => $balance,
                'recent_transactions' => $account->transactions,
                'trend' => $this->calculateTrend($account->id)
            ];
        });

        $this->totalBalance = $this->accountsData->sum('balance');
        $this->totalIncome = BankTransaction::where('transaction_type', 'credit')->sum('amount');
        $this->totalExpense = BankTransaction::where('transaction_type', 'debit')->sum('amount');
    }

    private function calculateAccountBalance($accountId)
    {
        $account = BankAccount::find($accountId);
        if (!$account) return 0;
        
        $credits = BankTransaction::where('bank_account_id', $accountId)
            ->where('transaction_type', 'credit')
            ->sum('amount');
        $debits = BankTransaction::where('bank_account_id', $accountId)
            ->where('transaction_type', 'debit')
            ->sum('amount');
        
        return $account->initial_balance + $credits - $debits;
    }

    private function calculateTrend($accountId)
    {
        $thisMonth = BankTransaction::where('bank_account_id', $accountId)
            ->where('transaction_type', 'credit')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('amount');
            
        $lastMonth = BankTransaction::where('bank_account_id', $accountId)
            ->where('transaction_type', 'credit')
            ->whereMonth('transaction_date', now()->subMonth()->month)
            ->whereYear('transaction_date', now()->subMonth()->year)
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
        if (!$this->selectedAccountId) {
            return collect()->paginate(10);
        }

        $query = BankTransaction::with('bankAccount')
            ->where('bank_account_id', $this->selectedAccountId)
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

    public function getCashflowData()
    {
        return collect(range(5, 0))->map(function($monthsBack) {
            $date = now()->subMonths($monthsBack);
            
            $baseQuery = BankTransaction::whereMonth('transaction_date', $date->month)
                ->whereYear('transaction_date', $date->year);
            
            // Filter by selected account if one is chosen
            if ($this->selectedAccountId) {
                $baseQuery->where('bank_account_id', $this->selectedAccountId);
            }
            
            $income = (clone $baseQuery)->where('transaction_type', 'credit')->sum('amount');
            $expense = (clone $baseQuery)->where('transaction_type', 'debit')->sum('amount');
            
            return [
                'month' => $date->format('M'),
                'income' => (int) $income,
                'expense' => (int) $expense
            ];
        })->values()->toArray();
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