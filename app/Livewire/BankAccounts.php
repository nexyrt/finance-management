<?php

namespace App\Livewire;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BankAccounts extends Component
{
    use WithPagination;

    // Bank account form properties
    #[Rule('required|string|max:255')]
    public $account_name = '';

    #[Rule('required|string|max:255')]
    public $account_number = '';

    #[Rule('required|string|max:255')]
    public $bank_name = '';

    #[Rule('nullable|string|max:255')]
    public $branch = '';

    #[Rule('required|string|size:3')]
    public $currency = 'IDR';

    #[Rule('required|numeric|min:0')]
    public $initial_balance = 0;

    // Transaction form properties
    #[Rule('required|numeric|min:0.01')]
    public $transaction_amount = '';

    #[Rule('required|date')]
    public $transaction_date = '';

    #[Rule('required|in:deposit,withdrawal,transfer,fee,interest')]
    public $transaction_type = 'deposit';

    #[Rule('nullable|string|max:255')]
    public $reference_number = '';

    #[Rule('nullable|string')]
    public $description = '';

    // Search & Filter properties
    public $search = '';
    public $sortField = 'account_name';
    public $sortDirection = 'asc';
    public $dateRangeStart = null;
    public $dateRangeEnd = null;
    public $dateFilter = ''; // Combined date range field
    public $transactionTypeFilter = '';

    // UI state properties
    public $showAccountFormModal = false;
    public $showDeleteConfirmModal = false;
    public $showDetailsModal = false;
    public $showTransactionFormModal = false;
    public $editMode = false;
    public $accountId = null;
    public $accountToDelete = null;
    public $selectedAccount = null;

    // Options arrays
    protected $currencyOptions = [
        ['value' => 'IDR', 'label' => 'Indonesian Rupiah (IDR)'],
        ['value' => 'USD', 'label' => 'US Dollar (USD)'],
        ['value' => 'EUR', 'label' => 'Euro (EUR)'],
        ['value' => 'SGD', 'label' => 'Singapore Dollar (SGD)'],
        ['value' => 'MYR', 'label' => 'Malaysian Ringgit (MYR)'],
    ];

    protected $transactionTypeOptions = [
        ['value' => 'deposit', 'label' => 'Deposit'],
        ['value' => 'withdrawal', 'label' => 'Withdrawal'],
        ['value' => 'transfer', 'label' => 'Transfer'],
        ['value' => 'fee', 'label' => 'Fee'],
        ['value' => 'interest', 'label' => 'Interest'],
    ];

    // Lifecycle methods
    public function mount()
    {
        $this->initializeDefaultValues();
    }

    // Computed properties
    #[Computed]
    public function bankAccounts()
    {
        return BankAccount::when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('account_name', 'like', '%' . $this->search . '%')
                        ->orWhere('account_number', 'like', '%' . $this->search . '%')
                        ->orWhere('bank_name', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);
    }

    #[Computed]
    public function accountTransactions()
    {
        if (!$this->selectedAccount) {
            return collect();
        }

        Log::info('Fetching transactions with date range', [
            'dateRangeStart' => $this->dateRangeStart,
            'dateRangeEnd' => $this->dateRangeEnd
        ]);

        $query = BankTransaction::where('bank_account_id', $this->selectedAccount->id);
        
        if ($this->dateRangeStart && $this->dateRangeEnd) {
            $query->whereBetween('transaction_date', [
                $this->dateRangeStart,
                $this->dateRangeEnd
            ]);
        }
        
        if ($this->transactionTypeFilter) {
            $query->where('transaction_type', $this->transactionTypeFilter);
        }
        
        $transactions = $query->orderBy('transaction_date', 'desc')->paginate(10);
        
        return $transactions;
    }

    #[Computed]
    public function accountsStats()
    {
        $totalAccounts = BankAccount::count();
        $totalBalance = BankAccount::sum('current_balance');
        $currencyGroups = BankAccount::selectRaw('currency, SUM(current_balance) as total')
            ->groupBy('currency')
            ->get();

        return [
            'totalAccounts' => $totalAccounts,
            'totalBalance' => $totalBalance,
            'currencyGroups' => $currencyGroups,
        ];
    }

    #[Computed]
    public function transactionStats()
    {
        if (!$this->selectedAccount) {
            return null;
        }

        // Create and clone queries for accurate sum calculations
        $baseQuery = BankTransaction::where('bank_account_id', $this->selectedAccount->id);
        
        if ($this->dateRangeStart && $this->dateRangeEnd) {
            $baseQuery->whereBetween('transaction_date', [
                $this->dateRangeStart,
                $this->dateRangeEnd
            ]);
        }
        
        // Clone queries to avoid SQL errors
        $incomingQuery = clone $baseQuery;
        $outgoingQuery = clone $baseQuery;
        
        $incoming = $incomingQuery->where('amount', '>', 0)->sum('amount');
        $outgoing = $outgoingQuery->where('amount', '<', 0)->sum('amount');

        return [
            'incoming' => $incoming,
            'outgoing' => abs($outgoing),
            'net' => $incoming + $outgoing,
        ];
    }

    // Helper methods
    protected function initializeDefaultValues()
    {
        $this->transaction_date = now()->format('Y-m-d');
        $this->dateRangeStart = now()->startOfMonth()->format('Y-m-d');
        $this->dateRangeEnd = now()->endOfMonth()->format('Y-m-d');
        
        // Set the formatted date range string for display
        $startFormatted = Carbon::parse($this->dateRangeStart)->format('d/m/Y');
        $endFormatted = Carbon::parse($this->dateRangeEnd)->format('d/m/Y');
        $this->dateFilter = "{$startFormatted} - {$endFormatted}";
        
        Log::info('BankAccount component mounted', [
            'dateRangeStart' => $this->dateRangeStart,
            'dateRangeEnd' => $this->dateRangeEnd,
            'dateFilter' => $this->dateFilter
        ]);
    }

    protected function convertDateFormat($value, $outputFormat = 'Y-m-d')
    {
        if (!$value) return null;
        
        try {
            // Handle DD/MM/YYYY format
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value)) {
                return Carbon::createFromFormat('d/m/Y', $value)->format($outputFormat);
            }
            
            // Handle Y-m-d format (already correct)
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                return $value;
            }
            
            // Try to parse any other date format
            return Carbon::parse($value)->format($outputFormat);
        } 
        catch (\Exception $e) {
            Log::error('Error parsing date', [
                'value' => $value,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    // Event handlers
    public function updatedDateFilter($value)
    {
        Log::info('Date filter updated', ['value' => $value]);
        
        if (empty($value)) {
            $this->dateRangeStart = null;
            $this->dateRangeEnd = null;
            return;
        }
        
        // Parse the combined date range string
        $dates = explode(' - ', $value);
        
        if (count($dates) === 2) {
            $this->dateRangeStart = $this->convertDateFormat($dates[0]);
            $this->dateRangeEnd = $this->convertDateFormat($dates[1]);
            
            Log::info('Date range parsed', [
                'start' => $this->dateRangeStart,
                'end' => $this->dateRangeEnd
            ]);
        }
    }

    public function updatedTransactionTypeFilter()
    {
        $this->dispatch('filterChanged');
    }

    public function updatedTransactionDate($value)
    {
        $formattedDate = $this->convertDateFormat($value);
        if ($formattedDate) {
            $this->transaction_date = $formattedDate;
        }
    }

    // Account actions
    public function createAccount()
    {
        $this->reset([
            'account_name', 'account_number', 'bank_name', 
            'branch', 'initial_balance', 'accountId', 'editMode'
        ]);
        $this->currency = 'IDR'; // Default currency
        $this->editMode = false;
        $this->showAccountFormModal = true;
    }

    public function editAccount($accountId)
    {
        try {
            $account = BankAccount::findOrFail($accountId);
            $this->accountId = $account->id;
            $this->account_name = $account->account_name;
            $this->account_number = $account->account_number;
            $this->bank_name = $account->bank_name;
            $this->branch = $account->branch;
            $this->currency = $account->currency;
            $this->initial_balance = $account->initial_balance;
            $this->editMode = true;
            $this->showAccountFormModal = true;
        } 
        catch (\Exception $e) {
            $this->showError('Error loading bank account: ' . $e->getMessage());
        }
    }

    public function saveAccount()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $accountData = [
                'account_name' => $this->account_name,
                'account_number' => $this->account_number,
                'bank_name' => $this->bank_name,
                'branch' => $this->branch,
                'currency' => $this->currency,
            ];

            if ($this->editMode) {
                // Update existing account
                $account = BankAccount::findOrFail($this->accountId);
                $account->update($accountData);
                $this->showMessage('Bank account updated successfully!');
            } 
            else {
                // Create new account
                $accountData['initial_balance'] = $this->initial_balance;
                $accountData['current_balance'] = $this->initial_balance;
                
                $account = BankAccount::create($accountData);
                
                // Create initial transaction if needed
                if ($this->initial_balance > 0) {
                    BankTransaction::create([
                        'bank_account_id' => $account->id,
                        'amount' => $this->initial_balance,
                        'transaction_date' => now(),
                        'transaction_type' => 'deposit',
                        'description' => 'Initial balance',
                        'reference_number' => 'INIT-' . uniqid(),
                    ]);
                }
                
                $this->showMessage('Bank account created successfully!');
            }

            DB::commit();
            $this->showAccountFormModal = false;
            $this->reset(['account_name', 'account_number', 'bank_name', 'branch', 'initial_balance', 'accountId', 'editMode']);
        } 
        catch (\Exception $e) {
            DB::rollBack();
            $this->showError('Error saving bank account: ' . $e->getMessage());
        }
    }

    public function cancelAccountForm()
    {
        $this->showAccountFormModal = false;
        $this->reset(['account_name', 'account_number', 'bank_name', 'branch', 'initial_balance', 'currency', 'accountId', 'editMode']);
    }
    
    public function confirmDelete($accountId)
    {
        $this->accountToDelete = BankAccount::findOrFail($accountId);
        $this->showDeleteConfirmModal = true;
    }

    public function deleteAccount()
    {
        try {
            if (!$this->accountToDelete) {
                throw new \Exception("No account selected for deletion");
            }

            // Check if account has transactions or payments
            $transactionCount = $this->accountToDelete->transactions()->count();
            $paymentCount = $this->accountToDelete->payments()->count();

            if ($transactionCount > 0 || $paymentCount > 0) {
                throw new \Exception("Cannot delete account with associated transactions or payments");
            }

            $this->accountToDelete->delete();
            $this->showMessage('Bank account deleted successfully!');
        } 
        catch (\Exception $e) {
            $this->showError('Error deleting bank account: ' . $e->getMessage());
        }

        $this->showDeleteConfirmModal = false;
        $this->accountToDelete = null;
    }

    // Transaction actions
    public function viewAccountDetails($accountId)
    {
        try {
            $this->selectedAccount = BankAccount::findOrFail($accountId);
            $this->showDetailsModal = true;
        } 
        catch (\Exception $e) {
            $this->showError('Error loading bank account details: ' . $e->getMessage());
        }
    }
    
    public function openTransactionForm($accountId)
    {
        try {
            $this->selectedAccount = BankAccount::findOrFail($accountId);
            $this->transaction_date = now()->format('Y-m-d');
            $this->transaction_type = 'deposit';
            $this->transaction_amount = '';
            $this->reference_number = '';
            $this->description = '';
            $this->showTransactionFormModal = true;
        } 
        catch (\Exception $e) {
            $this->showError('Error opening transaction form: ' . $e->getMessage());
        }
    }

    public function saveTransaction()
    {
        $this->validate([
            'transaction_amount' => 'required|numeric|min:0.01',
            'transaction_date' => 'required|date',
            'transaction_type' => 'required|in:deposit,withdrawal,transfer,fee,interest',
            'reference_number' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Calculate actual amount based on transaction type
            $amount = $this->transaction_amount;
            if (in_array($this->transaction_type, ['withdrawal', 'transfer', 'fee'])) {
                $amount = -$amount;

                // Check if there's enough balance
                if ($this->selectedAccount->current_balance + $amount < 0) {
                    throw new \Exception("Insufficient funds for this transaction");
                }
            }

            // Create transaction
            $transaction = BankTransaction::create([
                'bank_account_id' => $this->selectedAccount->id,
                'amount' => $amount,
                'transaction_date' => $this->transaction_date,
                'transaction_type' => $this->transaction_type,
                'description' => $this->description,
                'reference_number' => $this->reference_number,
            ]);

            // Update account balance
            $this->selectedAccount->current_balance += $amount;
            $this->selectedAccount->save();

            DB::commit();
            $this->showMessage('Transaction recorded successfully!');
            $this->showTransactionFormModal = false;
        } 
        catch (\Exception $e) {
            DB::rollBack();
            $this->showError('Error creating transaction: ' . $e->getMessage());
        }
    }

    // Utility functions
    public function resetFilters()
    {
        $this->reset(['search', 'transactionTypeFilter']);
        
        // Reset date range to current month
        $this->dateRangeStart = now()->startOfMonth()->format('Y-m-d');
        $this->dateRangeEnd = now()->endOfMonth()->format('Y-m-d');
        
        // Update the formatted date range display
        $startFormatted = Carbon::parse($this->dateRangeStart)->format('d/m/Y');
        $endFormatted = Carbon::parse($this->dateRangeEnd)->format('d/m/Y');
        $this->dateFilter = "{$startFormatted} - {$endFormatted}";
        
        $this->dispatch('filtersReset');
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }
    
    protected function showMessage($message)
    {
        session()->flash('message', $message);
    }
    
    protected function showError($message)
    {
        session()->flash('error', $message);
        Log::error($message);
    }
    
    // Getter methods for templates
    public function getCurrencyOptions()
    {
        return $this->currencyOptions;
    }
    
    public function getTransactionTypeOptions()
    {
        return $this->transactionTypeOptions;
    }

    public function render()
    {
        return view('livewire.bank-accounts', [
            'currencyOptions' => $this->currencyOptions,
            'transactionTypeOptions' => $this->transactionTypeOptions
        ]);
    }
}