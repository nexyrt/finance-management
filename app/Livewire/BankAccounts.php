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
    public $transfer_date = '';

    // Modal State
    public $showAddAccountModal = false;
    public $showEditAccountModal = false;
    public $showAddTransactionModal = false;
    public $showTransferModal = false;
    public $showDeleteModal = false;
    public $showAllTransactionsModal = false;
    public $showDeleteTransactionModal = false; // New modal state

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
    public $transactionToDelete = null; // New property for transaction deletion

    protected function rules()
    {
        $accountId = $this->editingAccount ? $this->editingAccount->id : null;

        return [
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50|unique:bank_accounts,account_number,' . $accountId,
            'bank_name' => 'required|string|max:255',
            'branch' => 'nullable|string|max:255',
            'current_balance' => 'required|numeric|min:0',
        ];
    }

    protected $transactionRules = [
        'transaction_amount' => 'required|numeric|min:1',
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
            'transfer_amount' => 'required|numeric|min:1',
            'transfer_date' => 'required|date|before_or_equal:today',
            'transfer_description' => 'nullable|string|max:500',
            'transfer_reference' => 'nullable|string|max:100',
        ];
    }

    public function mount()
    {
        $this->transaction_date = Carbon::now()->format('Y-m-d');
        $this->transfer_date = Carbon::now()->format('Y-m-d');
    }

    // Currency input value updaters - Handle raw values dari Alpine.js
    public function updatedCurrentBalance($value)
    {
        // Value dari Alpine.js adalah integer mentah (contoh: 50000000 untuk Rp 50.000.000)
        // Tidak perlu konversi, langsung simpan sebagai raw value untuk database
        $maxValue = 999999999999999; // 15 digit maksimum untuk DECIMAL(15,2)

        if ($value > $maxValue) {
            $this->addError('current_balance', 'Nilai terlalu besar. Maksimum adalah Rp ' . number_format($maxValue, 0, ',', '.'));
            return;
        }

        $this->current_balance = $value;
    }

    public function updatedTransactionAmount($value)
    {
        // Value dari Alpine.js adalah integer mentah
        $maxValue = 999999999999999; // 15 digit maksimum untuk DECIMAL(15,2)

        if ($value > $maxValue) {
            $this->addError('transaction_amount', 'Nilai terlalu besar. Maksimum adalah Rp ' . number_format($maxValue, 0, ',', '.'));
            return;
        }

        $this->transaction_amount = $value;
    }

    public function updatedTransferAmount($value)
    {
        // Value dari Alpine.js adalah integer mentah
        $maxValue = 999999999999999; // 15 digit maksimum untuk DECIMAL(15,2)

        if ($value > $maxValue) {
            $this->addError('transfer_amount', 'Nilai terlalu besar. Maksimum adalah Rp ' . number_format($maxValue, 0, ',', '.'));
            return;
        }

        $this->transfer_amount = $value;
    }

    // Livewire 3.0 Computed Properties
    #[Computed]
    public function totalBalance()
    {
        $total = BankAccount::sum('current_balance');
        return 'Rp ' . number_format($total, 0, ',', '.');
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
        return BankAccount::select('id', 'bank_name', 'account_number', 'current_balance')
            ->get()
            ->map(function ($account) {
                $account->formatted_balance = $this->formatAccountBalance($account);
                return $account;
            });
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

    // New method for confirming transaction deletion
    public function confirmDeleteTransaction($transactionId)
    {
        $this->transactionToDelete = BankTransaction::with('bankAccount')->findOrFail($transactionId);
        $this->showDeleteTransactionModal = true;
    }

    // Bank Account CRUD
    public function saveAccount()
    {
        $this->validate($this->rules());

        try {
            BankAccount::create([
                'account_name' => $this->account_name,
                'account_number' => $this->account_number,
                'bank_name' => $this->bank_name,
                'branch' => $this->branch ?: null,
                'initial_balance' => $this->current_balance, // Raw value dari currency input
                'current_balance' => $this->current_balance, // Raw value dari currency input
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
        $this->validate($this->rules());

        try {
            $this->editingAccount->update([
                'account_name' => $this->account_name,
                'account_number' => $this->account_number,
                'bank_name' => $this->bank_name,
                'branch' => $this->branch,
                'current_balance' => $this->current_balance, // Raw value dari currency input
            ]);

            $this->dispatch('notify', type: 'success', message: 'Bank account berhasil diperbarui!');
            $this->showEditAccountModal = false;
            $this->resetAccountForm();
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Gagal memperbarui bank account: ' . $e->getMessage());
        }
    }

    public function deleteAccount()
    {
        try {
            DB::transaction(function () {
                // Delete semua transaksi terkait terlebih dahulu
                $this->accountToDelete->transactions()->delete();

                // Kemudian delete account
                $this->accountToDelete->delete();
            });

            $this->dispatch('notify', type: 'success', message: 'Bank account dan semua transaksi terkait berhasil dihapus!');
            $this->showDeleteModal = false;
            $this->accountToDelete = null;
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Gagal menghapus bank account: ' . $e->getMessage());
        }
    }

    // New method for deleting transaction
    public function deleteTransaction()
    {
        try {
            DB::transaction(function () {
                $transaction = $this->transactionToDelete;
                $bankAccount = $transaction->bankAccount;

                // Reverse the transaction effect on bank account balance
                if ($transaction->transaction_type === 'credit') {
                    // If it was a credit (money in), subtract it from current balance
                    $bankAccount->decrement('current_balance', $transaction->amount);
                } else {
                    // If it was a debit (money out), add it back to current balance
                    $bankAccount->increment('current_balance', $transaction->amount);
                }

                // Delete the transaction
                $transaction->delete();
            });

            $this->dispatch('notify', type: 'success', message: 'Transaksi berhasil dihapus dan saldo bank telah disesuaikan!');
            $this->showDeleteTransactionModal = false;
            $this->transactionToDelete = null;

            // Reset pagination if needed
            $this->resetPage('transactionsPage');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Gagal menghapus transaksi: ' . $e->getMessage());
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
                    'amount' => $this->transaction_amount, // Raw value dari currency input
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
    public function processTransfer()
    {
        $this->validate($this->transferRules());

        try {
            DB::transaction(function () {
                $fromAccount = BankAccount::findOrFail($this->transfer_from_account);
                $toAccount = BankAccount::findOrFail($this->transfer_to_account);

                // Validate sufficient balance
                if ($fromAccount->current_balance < $this->transfer_amount) {
                    throw new \Exception('Saldo tidak mencukupi untuk transfer.');
                }

                $transferReference = $this->transfer_reference ?: 'TRF-' . Carbon::now()->format('YmdHis');

                // Create debit transaction for from account
                BankTransaction::create([
                    'bank_account_id' => $this->transfer_from_account,
                    'amount' => $this->transfer_amount,
                    'transaction_date' => $this->transfer_date, // UBAH DARI Carbon::now()->format('Y-m-d')
                    'transaction_type' => 'debit',
                    'description' => 'Transfer ke ' . $toAccount->bank_name . ' - ' . $toAccount->account_number .
                        ($this->transfer_description ? '. ' . $this->transfer_description : ''),
                    'reference_number' => $transferReference,
                ]);

                // Create credit transaction for to account
                BankTransaction::create([
                    'bank_account_id' => $this->transfer_to_account,
                    'amount' => $this->transfer_amount,
                    'transaction_date' => $this->transfer_date, // UBAH DARI Carbon::now()->format('Y-m-d')
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
    }

    // Reset forms
    private function resetAccountForm()
    {
        $this->account_name = '';
        $this->account_number = '';
        $this->bank_name = '';
        $this->branch = '';
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
        $this->transfer_date = Carbon::now()->format('Y-m-d'); // TAMBAHKAN INI
        $this->transfer_description = '';
        $this->transfer_reference = '';
        $this->resetErrorBag();
    }

    // Utility Methods
    public function formatCurrency($amount)
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    public function formatAccountBalance($account)
    {
        $balance = is_object($account) ? $account->current_balance : $account;
        return 'Rp ' . number_format($balance, 0, ',', '.');
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
    }

    public function updatingTransactionFilterType()
    {
        $this->resetPage('transactionsPage');
    }

    public function updatingTransactionDateRange()
    {
        $this->resetPage('transactionsPage');
    }

    public function render()
    {
        return view('livewire.bank-accounts');
    }
}