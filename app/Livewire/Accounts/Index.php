<?php

namespace App\Livewire\Accounts;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use Illuminate\Support\Facades\DB;
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
    public bool $ready = false;
    public bool $guideModal = false;

    public function mount(): void
    {
        // Defer heavy data loading to wire:init for faster initial render
    }

    public function loadData(): void
    {
        $this->ready = true;

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

        $this->toast()->success(__('pages.account_selected'), __('pages.viewing_account_data'))->send();
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
        $this->toast()->success(__('pages.all_data_refreshed'))->send();
    }

    // Event listeners from child components
    #[On('account-created', 'account-updated', 'account-deleted', 'transaction-created', 'transaction-deleted', 'transfer-completed', 'payment-deleted', 'transactions-updated', 'payments-updated', 'refresh-child-components')]
    public function refreshData(): void
    {
        $this->toast()->success(__('pages.data_updated'), __('pages.information_refreshed'))->send();
    }

    // Computed properties
    #[Computed]
    public function accountsData()
    {
        $accounts = BankAccount::with(['transactions', 'payments'])->get();

        // Batch trend calculation: 1 query instead of 2 per account
        $accountIds = $accounts->pluck('id');
        $trends = DB::table('bank_transactions')
            ->whereIn('bank_account_id', $accountIds)
            ->where('transaction_type', 'credit')
            ->whereIn(DB::raw('MONTH(transaction_date)'), [now()->month, now()->subMonth()->month])
            ->selectRaw('bank_account_id, MONTH(transaction_date) as m, SUM(amount) as total')
            ->groupBy('bank_account_id', DB::raw('MONTH(transaction_date)'))
            ->get()
            ->groupBy('bank_account_id');

        $thisMonth = now()->month;
        $lastMonth = now()->subMonth()->month;

        return $accounts->map(function ($account) use ($trends, $thisMonth, $lastMonth) {
            $accountTrends = $trends->get($account->id, collect());
            $thisMonthTotal = $accountTrends->firstWhere('m', $thisMonth)?->total ?? 0;
            $lastMonthTotal = $accountTrends->firstWhere('m', $lastMonth)?->total ?? 0;

            return [
                'id' => $account->id,
                'name' => $account->account_name,
                'bank' => $account->bank_name,
                'account_number' => $account->account_number,
                'balance' => $account->balance,
                'recent_transactions' => $account->transactions->sortByDesc('transaction_date')->take(3),
                'trend' => $thisMonthTotal >= $lastMonthTotal ? 'up' : 'down',
            ];
        });
    }

    #[Computed]
    public function totalBalance()
    {
        return $this->accountsData->sum('balance');
    }
}