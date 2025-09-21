<?php

namespace App\Livewire\Accounts;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Renderless;
use TallStackUi\Traits\Interactions;

class Index extends Component
{
    use Interactions;

    // Core state
    public $selectedAccountId = null;

    public function mount(): void
    {
        if ($this->accountsData->count() > 0) {
            $this->selectedAccountId = $this->accountsData->first()['id'];
        }
    }

    public function render()
    {
        return view('livewire.accounts.index');
    }

    // Account management
    public function selectAccount($accountId = null): void
    {
        $this->selectedAccountId = $accountId;

        // Notify all child components
        $this->dispatch('account-selected', accountId: $accountId);

        $this->toast()->success('Account Selected', 'Viewing data for selected account')->send();
    }

    public function createAccount(): void
    {
        $this->dispatch('open-create-account-modal');
    }

    public function editAccount($accountId): void
    {
        $this->dispatch('edit-account', accountId: $accountId);
    }

    public function deleteAccount($accountId): void
    {
        $this->dispatch('delete-account', accountId: $accountId);
    }

    #[On('refresh-data')]
    public function handleRefresh(): void
    {
        // Atau dispatch ke komponen spesifik
        $this->dispatch('refresh-transactions');
        $this->dispatch('refresh-payments');
        $this->dispatch('refresh-quick-actions');
    }

    // Method untuk manual refresh
    public function refreshAllData(): void
    {
        $this->dispatch('refresh-child-components');
        $this->toast()->success('All Data Refreshed')->send();
    }

    // Event listeners from child components
    #[On('account-created', 'account-updated', 'account-deleted', 'transaction-created', 'transaction-deleted', 'transfer-completed', 'payment-deleted', 'transactions-updated', 'payments-updated', 'refresh-child-components')]
    public function refreshData(): void
    {
        $this->toast()->success('Data Updated', 'Information has been refreshed')->send();
    }

    // Computed properties
    #[Computed]
    public function accountsData()
    {
        return BankAccount::with([
            'transactions' => fn($query) => $query->latest()->take(3)
        ])->get()->map(function ($account) {
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
    }

    // Helper methods
    private function calculateTrend($accountId): string
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
}