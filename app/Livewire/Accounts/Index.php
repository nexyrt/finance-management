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
    public string $activeTab = 'transactions';

    // Filters (passed to child table components)
    public string $search = '';
    public string $transactionType = '';
    public array $dateRange = [];

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

    // Tab switching
    #[Renderless]
    public function switchTab($tab): void
    {
        $this->activeTab = $tab;
    }

    // Quick actions
    public function addTransaction(): void
    {
        if (!$this->selectedAccountId) {
            $this->toast()->warning('Warning', 'Please select an account first')->send();
            return;
        }
        $this->dispatch('open-transaction-modal', accountId: $this->selectedAccountId);
    }

    public function transferFunds(): void
    {
        if (!$this->selectedAccountId) {
            $this->toast()->warning('Warning', 'Please select an account first')->send();
            return;
        }
        $this->dispatch('open-transfer-modal', fromAccountId: $this->selectedAccountId);
    }

    public function exportReport(): void
    {
        if (!$this->selectedAccountId) {
            $this->toast()->warning('Warning', 'Please select an account first')->send();
            return;
        }
        $this->toast()->info('Export Started', 'Your report is being generated')->send();
    }

    // Filter management
    public function clearFilters(): void
    {
        $this->search = '';
        $this->transactionType = '';
        $this->dateRange = [];
        $this->toast()->info('Filters Cleared', 'All filters have been reset')->send();
    }

    // Event listeners from child components and modals
    #[On('account-created', 'account-updated', 'account-deleted', 'transaction-created', 'transaction-deleted', 'transfer-completed', 'payment-deleted', 'transactions-updated', 'payments-updated')]
    public function refreshData(): void
    {
        // Dispatch chart update when data changes
        $this->dispatch('chartDataUpdated', [
            'chartData' => $this->chartData,
        ]);

        $this->toast()->success('Data Updated', 'Information has been refreshed')->send();
    }

    // Handle account selection change for chart
    public function updatedSelectedAccountId(): void
    {
        $this->dispatch('chartDataUpdated', [
            'chartData' => $this->chartData,
        ]);
    }

    // Handle events from child table components
    #[On('add-transaction')]
    public function handleAddTransaction(): void
    {
        $this->addTransaction();
    }

    #[On('delete-transaction')]
    public function handleDeleteTransaction($transactionId): void
    {
        $this->dispatch('delete-transaction', transactionId: $transactionId);
    }

    #[On('delete-payment')]
    public function handleDeletePayment($paymentId): void
    {
        $this->dispatch('delete-payment', paymentId: $paymentId);
    }

    // Chart data
    #[Computed]
    public function chartData(): array
    {
        if (!$this->selectedAccountId) {
            return [];
        }

        $months = collect();
        $currentDate = now()->startOfMonth()->subMonths(11); // Last 12 months

        for ($i = 0; $i < 12; $i++) {
            $monthStart = $currentDate->copy()->addMonths($i);
            $monthEnd = $monthStart->copy()->endOfMonth();

            // Income dari payments + credit transactions
            $paymentsIncome = \App\Models\Payment::where('bank_account_id', $this->selectedAccountId)
                ->whereBetween('payment_date', [$monthStart, $monthEnd])
                ->sum('amount');

            $transactionsIncome = \App\Models\BankTransaction::where('bank_account_id', $this->selectedAccountId)
                ->where('transaction_type', 'credit')
                ->whereBetween('transaction_date', [$monthStart, $monthEnd])
                ->sum('amount');

            // Expense dari debit transactions
            $expense = \App\Models\BankTransaction::where('bank_account_id', $this->selectedAccountId)
                ->where('transaction_type', 'debit')
                ->whereBetween('transaction_date', [$monthStart, $monthEnd])
                ->sum('amount');

            $months->push([
                'month' => $monthStart->format('M Y'),
                'income' => $paymentsIncome + $transactionsIncome,
                'expense' => $expense
            ]);
        }

        return $months->toArray();
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