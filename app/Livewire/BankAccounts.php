<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BankAccounts extends Component
{
    use WithPagination;

    // Bank Account Form Properties
    public $account_name = '';
    public $account_number = '';
    public $bank_name = '';
    public $branch = '';
    public $initial_balance = 0;
    public $current_balance = 0;

    // Transaction Form Properties
    public $transaction_amount = 0;
    public $transaction_date = '';
    public $transaction_type = 'credit';
    public $transaction_description = '';
    public $reference_number = '';
    public $selected_bank_account_id = null;

    // Transfer Form Properties
    public $transfer_from_account = null;
    public $transfer_to_account = null;
    public $transfer_amount = 0;
    public $transfer_description = '';
    public $transfer_reference = '';

    // Modal State
    public $showAddAccountModal = false;
    public $showEditAccountModal = false;
    public $showAddTransactionModal = false;
    public $showTransferModal = false;
    public $showDeleteModal = false;
    public $showAllTransactionsModal = false;

    // Search and Filter
    public $search = '';
    public $filterBank = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    // Transaction Filters for All Transactions Modal
    public $transactionFilterBank = '';
    public $transactionFilterType = '';
    public $transactionDateRange = '';

    // Edit State
    public $editingAccount = null;
    public $accountToDelete = null;

    protected $rules = [
        'account_name' => 'required|string|max:255',
        'account_number' => 'required|string|max:50|unique:bank_accounts,account_number',
        'bank_name' => 'required|string|max:255',
        'branch' => 'nullable|string|max:255',
        'initial_balance' => 'required|numeric|min:0',
        'current_balance' => 'required|numeric|min:0',
    ];

    protected $transactionRules = [
        'transaction_amount' => 'required|numeric|min:0.01',
        'transaction_date' => 'required|date',
        'transaction_type' => 'required|in:credit,debit',
        'transaction_description' => 'nullable|string|max:500',
        'reference_number' => 'nullable|string|max:100',
        'selected_bank_account_id' => 'required|exists:bank_accounts,id',
    ];

    protected $transferRules = [
        'transfer_from_account' => 'required|exists:bank_accounts,id|different:transfer_to_account',
        'transfer_to_account' => 'required|exists:bank_accounts,id|different:transfer_from_account',
        'transfer_amount' => 'required|numeric|min:0.01',
        'transfer_description' => 'nullable|string|max:500',
        'transfer_reference' => 'nullable|string|max:100',
    ];

    public function mount()
    {
        $this->transaction_date = Carbon::now()->format('Y-m-d');
    }

    // All Transactions Modal
    public function openAllTransactionsModal()
    {
        $this->showAllTransactionsModal = true;
    }

    // Bank Account Management
    public function openAddAccountModal()
    {
        $this->resetAccountForm();
        $this->showAddAccountModal = true;
    }

    public function openEditAccountModal($accountId)
    {
        $this->editingAccount = BankAccount::findOrFail($accountId);
        $this->account_name = $this->editingAccount->account_name;
        $this->account_number = $this->editingAccount->account_number;
        $this->bank_name = $this->editingAccount->bank_name;
        $this->branch = $this->editingAccount->branch;
        $this->initial_balance = $this->editingAccount->initial_balance;
        $this->current_balance = $this->editingAccount->current_balance;
        $this->showEditAccountModal = true;
    }

    public function saveAccount()
    {
        $this->validate($this->rules);

        try {
            BankAccount::create([
                'account_name' => $this->account_name,
                'account_number' => $this->account_number,
                'bank_name' => $this->bank_name,
                'branch' => $this->branch,
                'initial_balance' => $this->initial_balance,
                'current_balance' => $this->current_balance,
            ]);

            $this->dispatch('notify', type: 'success', message: 'Bank account berhasil ditambahkan!');
            $this->showAddAccountModal = false;
            $this->resetAccountForm();
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Gagal menambahkan bank account: ' . $e->getMessage());
        }
    }

    public function updateAccount()
    {
        $rules = $this->rules;
        $rules['account_number'] = 'required|string|max:50|unique:bank_accounts,account_number,' . $this->editingAccount->id;

        $this->validate($rules);

        try {
            $this->editingAccount->update([
                'account_name' => $this->account_name,
                'account_number' => $this->account_number,
                'bank_name' => $this->bank_name,
                'branch' => $this->branch,
                'initial_balance' => $this->initial_balance,
                'current_balance' => $this->current_balance,
            ]);

            $this->dispatch('notify', type: 'success', message: 'Bank account berhasil diperbarui!');
            $this->showEditAccountModal = false;
            $this->resetAccountForm();
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Gagal memperbarui bank account: ' . $e->getMessage());
        }
    }

    public function confirmDeleteAccount($accountId)
    {
        $this->accountToDelete = BankAccount::findOrFail($accountId);
        $this->showDeleteModal = true;
    }

    public function deleteAccount()
    {
        try {
            DB::transaction(function () {
                // Delete related transactions first
                $this->accountToDelete->transactions()->delete();

                // Delete the account
                $this->accountToDelete->delete();
            });

            $this->dispatch('notify', type: 'success', message: 'Bank account berhasil dihapus!');
            $this->showDeleteModal = false;
            $this->accountToDelete = null;
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Gagal menghapus bank account: ' . $e->getMessage());
        }
    }

    // Transaction Management
    public function openAddTransactionModal($accountId = null)
    {
        $this->resetTransactionForm();
        if ($accountId) {
            $this->selected_bank_account_id = $accountId;
        }
        $this->showAddTransactionModal = true;
    }

    public function saveTransaction()
    {
        $this->validate($this->transactionRules);

        try {
            DB::transaction(function () {
                $bankAccount = BankAccount::findOrFail($this->selected_bank_account_id);

                // Create transaction
                BankTransaction::create([
                    'bank_account_id' => $this->selected_bank_account_id,
                    'amount' => $this->transaction_amount,
                    'transaction_date' => $this->transaction_date,
                    'transaction_type' => $this->transaction_type,
                    'description' => $this->transaction_description,
                    'reference_number' => $this->reference_number,
                ]);

                // Update bank account balance
                if ($this->transaction_type === 'credit') {
                    $bankAccount->increment('current_balance', $this->transaction_amount);
                } else {
                    $bankAccount->decrement('current_balance', $this->transaction_amount);
                }
            });

            $this->dispatch('notify', type: 'success', message: 'Transaksi berhasil ditambahkan!');
            $this->showAddTransactionModal = false;
            $this->resetTransactionForm();
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Gagal menambahkan transaksi: ' . $e->getMessage());
        }
    }

    // Transfer Management
    public function openTransferModal()
    {
        $this->resetTransferForm();
        $this->showTransferModal = true;
    }

    public function processTransfer()
    {
        $this->validate($this->transferRules);

        try {
            DB::transaction(function () {
                $fromAccount = BankAccount::findOrFail($this->transfer_from_account);
                $toAccount = BankAccount::findOrFail($this->transfer_to_account);

                // Check if from account has sufficient balance
                if ($fromAccount->current_balance < $this->transfer_amount) {
                    throw new \Exception('Saldo tidak mencukupi untuk transfer.');
                }

                // Create debit transaction for from account
                BankTransaction::create([
                    'bank_account_id' => $this->transfer_from_account,
                    'amount' => $this->transfer_amount,
                    'transaction_date' => Carbon::now()->format('Y-m-d'),
                    'transaction_type' => 'debit',
                    'description' => 'Transfer ke ' . $toAccount->bank_name . ' - ' . $toAccount->account_number . '. ' . $this->transfer_description,
                    'reference_number' => $this->transfer_reference,
                ]);

                // Create credit transaction for to account
                BankTransaction::create([
                    'bank_account_id' => $this->transfer_to_account,
                    'amount' => $this->transfer_amount,
                    'transaction_date' => Carbon::now()->format('Y-m-d'),
                    'transaction_type' => 'credit',
                    'description' => 'Transfer dari ' . $fromAccount->bank_name . ' - ' . $fromAccount->account_number . '. ' . $this->transfer_description,
                    'reference_number' => $this->transfer_reference,
                ]);

                // Update balances
                $fromAccount->decrement('current_balance', $this->transfer_amount);
                $toAccount->increment('current_balance', $this->transfer_amount);
            });

            $this->dispatch('notify', type: 'success', message: 'Transfer berhasil diproses!');
            $this->showTransferModal = false;
            $this->resetTransferForm();
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Gagal memproses transfer: ' . $e->getMessage());
        }
    }

    // Helper Methods
    private function resetAccountForm()
    {
        $this->account_name = '';
        $this->account_number = '';
        $this->bank_name = '';
        $this->branch = '';
        $this->initial_balance = 0;
        $this->current_balance = 0;
        $this->editingAccount = null;
        $this->resetErrorBag();
    }

    private function resetTransactionForm()
    {
        $this->transaction_amount = 0;
        $this->transaction_date = Carbon::now()->format('Y-m-d');
        $this->transaction_type = 'credit';
        $this->transaction_description = '';
        $this->reference_number = '';
        $this->selected_bank_account_id = null;
        $this->resetErrorBag();
    }

    private function resetTransferForm()
    {
        $this->transfer_from_account = null;
        $this->transfer_to_account = null;
        $this->transfer_amount = 0;
        $this->transfer_description = '';
        $this->transfer_reference = '';
        $this->resetErrorBag();
    }

    // Reset pagination for transaction filters
    public function updatingTransactionFilterBank()
    {
        $this->resetPage('transactionsPage');
    }

    public function updatingTransactionFilterType()
    {
        $this->resetPage('transactionsPage');
    }

    public function updatingTransactionDateRange()
    {
        $this->resetPage('transactionsPage');
    }

    public function resetTransactionFilters()
    {
        $this->transactionFilterBank = '';
        $this->transactionFilterType = '';
        $this->transactionDateRange = '';
        $this->resetPage('transactionsPage');
    }



    // Calculated Properties
    public function getTotalBalanceProperty()
    {
        return BankAccount::sum('current_balance');
    }

    public function getTotalAccountsProperty()
    {
        return BankAccount::count();
    }

    public function getTodayTransactionsProperty()
    {
        return BankTransaction::whereDate('transaction_date', Carbon::today())->count();
    }

    public function getRecentTransactionsProperty()
    {
        return BankTransaction::with('bankAccount')
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'bank_name' => $transaction->bankAccount->bank_name,
                    'amount' => $transaction->amount,
                    'type' => $transaction->transaction_type,
                    'description' => $transaction->description,
                    'date' => $transaction->transaction_date,
                    'formatted_date' => Carbon::parse($transaction->transaction_date)->diffForHumans(),
                ];
            });
    }

    public function getBankAccountsProperty()
    {
        $query = BankAccount::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('account_name', 'like', '%' . $this->search . '%')
                    ->orWhere('bank_name', 'like', '%' . $this->search . '%')
                    ->orWhere('account_number', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterBank) {
            $query->where('bank_name', 'like', '%' . $this->filterBank . '%');
        }

        return $query->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);
    }

    public function getBalanceDistributionProperty()
    {
        $accounts = BankAccount::select('bank_name', 'current_balance')->get();
        $totalBalance = $accounts->sum('current_balance');

        if ($totalBalance == 0)
            return [];

        return $accounts->groupBy('bank_name')->map(function ($group, $bankName) use ($totalBalance) {
            $bankTotal = $group->sum('current_balance');
            return [
                'bank_name' => $bankName,
                'total_balance' => $bankTotal,
                'percentage' => round(($bankTotal / $totalBalance) * 100, 1),
            ];
        })->sortByDesc('total_balance')->values();
    }

    public function getAllTransactionsProperty()
    {
        // Return empty paginator instead of collection when modal is closed
        if (!$this->showAllTransactionsModal) {
            return new \Illuminate\Pagination\LengthAwarePaginator(
                collect(), // empty collection
                0, // total
                15, // per page
                1, // current page
                ['path' => request()->url(), 'pageName' => 'transactionsPage']
            );
        }

        $query = BankTransaction::with('bankAccount');

        // Apply filters
        if ($this->transactionFilterBank) {
            $query->where('bank_account_id', $this->transactionFilterBank);
        }

        if ($this->transactionFilterType) {
            $query->where('transaction_type', $this->transactionFilterType);
        }

        // Handle date range filter
        if ($this->transactionDateRange) {
            // Split the date range (format: "2024-01-01 to 2024-01-31")
            $dates = explode(' to ', $this->transactionDateRange);
            if (count($dates) === 2) {
                $query->whereDate('transaction_date', '>=', trim($dates[0]))
                    ->whereDate('transaction_date', '<=', trim($dates[1]));
            } elseif (count($dates) === 1) {
                // Single date selected
                $query->whereDate('transaction_date', '=', trim($dates[0]));
            }
        }

        return $query->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15, ['*'], 'transactionsPage');
    }

    // Utility Methods
    public function formatCurrency($amount)
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        return view('livewire.bank-accounts', [
            'bankAccounts' => $this->bankAccounts,
            'totalBalance' => $this->formatCurrency($this->totalBalance),
            'totalAccounts' => $this->totalAccounts,
            'todayTransactions' => $this->todayTransactions,
            'recentTransactions' => $this->recentTransactions,
            'balanceDistribution' => $this->balanceDistribution,
            'availableAccounts' => BankAccount::select('id', 'bank_name', 'account_number', 'current_balance')->get(),
            'allTransactions' => $this->showAllTransactionsModal ? $this->allTransactions : collect(),
        ]);
    }
}