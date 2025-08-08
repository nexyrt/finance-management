<?php

namespace App\Livewire\BankAccounts;

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

    public function render()
    {
        $accounts = BankAccount::query()
            ->when($this->search, function($query) {
                $query->where('account_name', 'like', '%' . $this->search . '%')
                      ->orWhere('bank_name', 'like', '%' . $this->search . '%')
                      ->orWhere('account_number', 'like', '%' . $this->search . '%');
            })
            ->when($this->bankFilter, function($query) {
                $query->where('bank_name', 'like', '%' . $this->bankFilter . '%');
            })
            ->latest()
            ->paginate(12);

        $totalBalance = BankAccount::all()->sum('current_balance');
        $totalAccounts = BankAccount::count();
        $activeAccounts = BankAccount::count();

        $recentTransactions = BankTransaction::with('bankAccount')
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.bank-accounts.index', [
            'accounts' => $accounts,
            'totalBalance' => $totalBalance,
            'totalAccounts' => $totalAccounts,
            'activeAccounts' => $activeAccounts,
            'recentTransactions' => $recentTransactions,
            'bankNames' => BankAccount::distinct()->pluck('bank_name')->filter()
        ]);
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->bankFilter = '';
    }
}