<?php

namespace App\Livewire\Accounts;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    // Search and filters
    public $search = '';
    public $bankFilter = '';

    // Stats properties - calculated once and cached
    public $totalBalance = 0;
    public $totalAccounts = 0;
    public $activeAccounts = 0;

    public function mount()
    {
        $this->calculateStats();
    }

    public function refreshData()
    {
        $this->resetPage();
        $this->calculateStats();
    }

    private function calculateStats()
    {
        // Fix: Use accessor instead of direct column
        $this->totalBalance = BankAccount::all()->sum(function ($account) {
            return $account->balance; // Uses getBalanceAttribute() accessor
        });
        
        $this->totalAccounts = BankAccount::count();
        $this->activeAccounts = BankAccount::count();
    }

    public function with(): array
    {
        return [
            'accounts' => $this->getAccounts(),
            'totalBalance' => $this->totalBalance,
            'totalAccounts' => $this->totalAccounts,
            'activeAccounts' => $this->activeAccounts,
            'recentTransactions' => $this->getRecentTransactions(),
            'bankNames' => BankAccount::distinct()->pluck('bank_name')->filter(),
        ];
    }

    private function getAccounts()
    {
        return BankAccount::query()
            ->when($this->search, function ($query) {
                $query->where('account_name', 'like', '%'.$this->search.'%')
                    ->orWhere('bank_name', 'like', '%'.$this->search.'%')
                    ->orWhere('account_number', 'like', '%'.$this->search.'%');
            })
            ->when($this->bankFilter, function ($query) {
                $query->where('bank_name', 'like', '%'.$this->bankFilter.'%');
            })
            ->latest()
            ->paginate(12);
    }

    private function getRecentTransactions()
    {
        return BankTransaction::with('bankAccount')
            ->latest()
            ->take(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.bank-accounts.index', $this->with());
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->bankFilter = '';
    }
}