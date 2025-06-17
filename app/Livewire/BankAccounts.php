<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;

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

    protected function rules()
    {
        $accountId = $this->editingAccount ? $this->editingAccount->id : null;
        
        return [
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50|unique:bank_accounts,account_number,' . $accountId,
            'bank_name' => 'required|string|max:255',
            'branch' => 'nullable|string|max:255',
            'initial_balance' => 'required|numeric|min:0',
            'current_balance' => 'required|numeric|min:0',
        ];
    }

    protected $transactionRules = [
        'transaction_amount' => 'required|numeric|min:0.01',
        'transaction_date' => 'required|date|before_or_equal:today',
        'transaction_type' => 'required|in:credit,debit',
        'transaction_description' => 'nullable|string|max:500',
        'reference_number' => 'nullable|string|max:100',
        'selected_bank_account_id' => 'required|exists:bank_accounts,id',
    ];

    protected function transferRules()
    {
        return [
            'transfer_from_account' => 'required|exists:bank_accounts,id|different:transfer_to_account',
            'transfer_to_account' => 'required|exists:bank_accounts,id|different:transfer_from_account',
            'transfer_amount' => 'required|numeric|min:0.01|max:' . ($this->getFromAccountBalance()),
            'transfer_description' => 'nullable|string|max:500',
            'transfer_reference' => 'nullable|string|max:100',
        ];
    }

    private function getFromAccountBalance()
    {
        if (!$this->transfer_from_account) {
            return 0;
        }
        
        $account = BankAccount::find($this->transfer_from_account);
        return $account ? $account->current_balance : 0;
    }

    public function mount()
    {
        $this->transaction_date = Carbon::now()->format('Y-m-d');
    }

    // Livewire 3.0 Computed Properties
    #[Computed]
    public function totalBalance()
    {
        return $this->formatCurrency(BankAccount::sum('current_balance'));
    }

    #[Computed] 
    public function totalAccounts()
    {
        return BankAccount::count();
    }

    #[Computed]
    public function todayTransactions()
    {
        return BankTransaction::whereDate('transaction_date', Carbon::today())->count();
    }

    #[Computed]
    public function recentTransactions()
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

    #[Computed]
    public function bankAccounts()
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

    #[Computed]
    public function balanceDistribution()
    {
        $accounts = BankAccount::select('bank_name', 'current_balance')->get();
        $totalBalance = $accounts->sum('current_balance');

        if ($totalBalance == 0) {
            return collect();
        }

        return $accounts->groupBy('bank_name')->map(function ($group, $bankName) use ($totalBalance) {
            $bankTotal = $group->sum('current_balance');
            return [
                'bank_name' => $bankName,
                'total_balance' => $bankTotal,
                'percentage' => round(($bankTotal / $totalBalance) * 100, 1),
            ];
        })->sortByDesc('total_balance')->values();
    }

    #[Computed]
    public function allTransactions()
    {
        if (!$this->showAllTransactionsModal) {
            return new \Illuminate\Pagination\LengthAwarePaginator(
                collect(),
                0,
                15,
                1,
                ['path' => request()->url(), 'pageName' => 'transactionsPage']
            );
        }

        $query = BankTransaction::with('bankAccount');

        if ($this->transactionFilterBank) {
            $query->where('bank_account_id', $this->transactionFilterBank);
        }

        if ($this->transactionFilterType) {
            $query->where('transaction_type', $this->transactionFilterType);
        }

        if ($this->transactionDateRange) {
            $this->applyDateRangeFilter($query);
        }

        return $query->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15, ['*'], 'transactionsPage');
    }

    #[Computed]
    public function availableAccounts()
    {
        return BankAccount::select('id', 'bank_name', 'account_number', 'current_balance')->get();
    }

    private function applyDateRangeFilter($query)
    {
        $dates = explode(' to ', $this->transactionDateRange);
        
        if (count($dates) === 2) {
            $query->whereDate('transaction_date', '>=', Carbon::parse(trim($dates[0])))
                  ->whereDate('transaction_date', '<=', Carbon::parse(trim($dates[1])));
        } elseif (count($dates) === 1 && !empty(trim($dates[0]))) {
            $query->whereDate('transaction_date', '=', Carbon::parse(trim($dates[0])));
        }
    }

    // Modal Management
    public function openAllTransactionsModal()
    {
        $this->showAllTransactionsModal = true;
    }

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

    public function openAddTransactionModal($accountId = null)
    {
        $this->resetTransactionForm();
        if ($accountId) {
            $this->selected_bank_account_id = $accountId;
        }
        $this->showAddTransactionModal = true;
    }

    public function openTransferModal()
    {
        $this->resetTransferForm();
        $this->showTransferModal = true;
    }

    public function confirmDeleteAccount($accountId)
    {
        $this->accountToDelete = BankAccount::findOrFail($accountId);
        $this->showDeleteModal = true;
    }

    // Bank Account CRUD
    public function saveAccount()
    {
        $this->validate($this->rules());

        try {
            DB::transaction(function () {
                BankAccount::create([
                    'account_name' => $this->account_name,
                    'account_number' => $this->account_number,
                    'bank_name' => $this->bank_name,
                    'branch' => $this->branch,
                    'initial_balance' => $this->initial_balance,
                    'current_balance' => $this->current_balance,
                ]);
            });

            $this->dispatch('notify', type: 'success', message: 'Bank account berhasil ditambahkan!');
            $this->showAddAccountModal = false;
            $this->resetAccountForm();
            
            // Clear computed property cache
            unset($this->totalBalance, $this->totalAccounts, $this->bankAccounts);
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Gagal menambahkan bank account: ' . $e->getMessage());
        }
    }

    public function updateAccount()
    {
        $this->validate($this->rules());

        try {
            DB::transaction(function () {
                $this->editingAccount->update([
                    'account_name' => $this->account_name,
                    'account_number' => $this->account_number,
                    'bank_name' => $this->bank_name,
                    'branch' => $this->branch,
                    'initial_balance' => $this->initial_balance,
                    'current_balance' => $this->current_balance,
                ]);
            });

            $this->dispatch('notify', type: 'success', message: 'Bank account berhasil diperbarui!');
            $this->showEditAccountModal = false;
            $this->resetAccountForm();
            
            // Clear computed property cache
            unset($this->totalBalance, $this->bankAccounts);
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Gagal memperbarui bank account: ' . $e->getMessage());
        }
    }

    public function deleteAccount()
    {
        try {
            DB::transaction(function () {
                // Check if account has transactions
                $transactionCount = $this->accountToDelete->transactions()->count();
                
                if ($transactionCount > 0) {
                    // Archive instead of delete
                    $this->accountToDelete->update([
                        'account_name' => $this->accountToDelete->account_name . ' (Archived)',
                        'current_balance' => 0,
                    ]);
                } else {
                    // Safe to delete
                    $this->accountToDelete->delete();
                }
            });

            $this->dispatch('notify', type: 'success', message: 'Bank account berhasil dihapus!');
            $this->showDeleteModal = false;
            $this->accountToDelete = null;
            
            // Clear computed property cache
            unset($this->totalBalance, $this->totalAccounts, $this->bankAccounts);
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Gagal menghapus bank account: ' . $e->getMessage());
        }
    }

    // Transaction Management
    public function saveTransaction()
    {
        $this->validate($this->transactionRules);

        try {
            DB::transaction(function () {
                $bankAccount = BankAccount::findOrFail($this->selected_bank_account_id);

                // Validate sufficient balance for debit transactions
                if ($this->transaction_type === 'debit' && $bankAccount->current_balance < $this->transaction_amount) {
                    throw new \Exception('Saldo tidak mencukupi untuk transaksi debit.');
                }

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
            
            // Clear computed property cache
            unset($this->totalBalance, $this->recentTransactions, $this->todayTransactions);
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Gagal menambahkan transaksi: ' . $e->getMessage());
        }
    }

    // Transfer Management
    public function processTransfer()
    {
        $this->validate($this->transferRules());

        try {
            DB::transaction(function () {
                $fromAccount = BankAccount::findOrFail($this->transfer_from_account);
                $toAccount = BankAccount::findOrFail($this->transfer_to_account);

                $transferReference = $this->transfer_reference ?: 'TRF-' . Carbon::now()->format('YmdHis');

                // Create debit transaction for from account
                BankTransaction::create([
                    'bank_account_id' => $this->transfer_from_account,
                    'amount' => $this->transfer_amount,
                    'transaction_date' => Carbon::now()->format('Y-m-d'),
                    'transaction_type' => 'debit',
                    'description' => 'Transfer ke ' . $toAccount->bank_name . ' - ' . $toAccount->account_number . 
                                   ($this->transfer_description ? '. ' . $this->transfer_description : ''),
                    'reference_number' => $transferReference,
                ]);

                // Create credit transaction for to account
                BankTransaction::create([
                    'bank_account_id' => $this->transfer_to_account,
                    'amount' => $this->transfer_amount,
                    'transaction_date' => Carbon::now()->format('Y-m-d'),
                    'transaction_type' => 'credit',
                    'description' => 'Transfer dari ' . $fromAccount->bank_name . ' - ' . $fromAccount->account_number . 
                                   ($this->transfer_description ? '. ' . $this->transfer_description : ''),
                    'reference_number' => $transferReference,
                ]);

                // Update balances
                $fromAccount->decrement('current_balance', $this->transfer_amount);
                $toAccount->increment('current_balance', $this->transfer_amount);
            });

            $this->dispatch('notify', type: 'success', message: 'Transfer berhasil diproses!');
            $this->showTransferModal = false;
            $this->resetTransferForm();
            
            // Clear computed property cache
            unset($this->totalBalance, $this->recentTransactions);
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Gagal memproses transfer: ' . $e->getMessage());
        }
    }

    // Filter Management
    public function resetTransactionFilters()
    {
        $this->transactionFilterBank = '';
        $this->transactionFilterType = '';
        $this->transactionDateRange = '';
        $this->resetPage('transactionsPage');
        
        // Clear computed property cache
        unset($this->allTransactions);
    }

    // Reset forms
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

    // Utility Methods
    public function formatCurrency($amount)
    {
        return 'Rp ' . number_format((float)$amount, 0, ',', '.');
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        
        // Clear computed property cache
        unset($this->bankAccounts);
    }

    // Livewire lifecycle hooks
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterBank()
    {
        $this->resetPage();
    }

    public function updatingTransactionFilterBank()
    {
        $this->resetPage('transactionsPage');
        unset($this->allTransactions);
    }

    public function updatingTransactionFilterType()
    {
        $this->resetPage('transactionsPage');
        unset($this->allTransactions);
    }

    public function updatingTransactionDateRange()
    {
        $this->resetPage('transactionsPage');
        unset($this->allTransactions);
    }

    public function render()
    {
        return view('livewire.bank-accounts');
    }
}