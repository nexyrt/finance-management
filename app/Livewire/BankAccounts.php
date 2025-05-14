<?php

namespace App\Livewire;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Computed;
use Flux\Livewire\Facades\Flux;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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

    // Search & Filter properties
    public $search = '';
    public $sortField = 'account_name';
    public $sortDirection = 'asc';

    // UI state control
    public $showAccountFormModal = false;
    public $showDeleteConfirmModal = false;
    public $showDetailsModal = false;
    public $editMode = false;
    public $accountId = null;
    public $accountToDelete = null;
    public $selectedAccount = null;
    
    // Transaction form
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
    
    public $showTransactionFormModal = false;
    public $dateRange = [
        'start' => null,
        'end' => null
    ];
    
    public $transactionTypeFilter = '';

    // Currency options
    public $currencyOptions = [
        ['value' => 'IDR', 'label' => 'Indonesian Rupiah (IDR)'],
        ['value' => 'USD', 'label' => 'US Dollar (USD)'],
        ['value' => 'EUR', 'label' => 'Euro (EUR)'],
        ['value' => 'SGD', 'label' => 'Singapore Dollar (SGD)'],
        ['value' => 'MYR', 'label' => 'Malaysian Ringgit (MYR)'],
    ];
    
    // Transaction type options
    public $transactionTypeOptions = [
        ['value' => 'deposit', 'label' => 'Deposit'],
        ['value' => 'withdrawal', 'label' => 'Withdrawal'],
        ['value' => 'transfer', 'label' => 'Transfer'],
        ['value' => 'fee', 'label' => 'Fee'],
        ['value' => 'interest', 'label' => 'Interest'],
    ];

    public function mount()
    {
        $this->transaction_date = now()->format('Y-m-d');
        
        // Default date range to current month
        $this->dateRange['start'] = now()->startOfMonth()->format('Y-m-d');
        $this->dateRange['end'] = now()->endOfMonth()->format('Y-m-d');
        
        Log::info('BankAccountManagement component mounted');
    }

    public function createAccount()
    {
        $this->reset([
            'account_name', 'account_number', 'bank_name', 
            'branch', 'initial_balance', 'accountId', 'editMode'
        ]);
        $this->currency = 'IDR'; // Default currency
        $this->editMode = false;
        $this->showAccountFormModal = true;
        
        Log::info('Opening create bank account modal');
    }

    public function editAccount($accountId)
    {
        Log::info('Editing bank account', ['account_id' => $accountId]);

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

            Log::info('Bank account loaded for editing', [
                'account_id' => $account->id,
                'account_name' => $account->account_name
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading bank account for edit', [
                'account_id' => $accountId,
                'error' => $e->getMessage()
            ]);

            session()->flash('error', 'Error loading bank account: ' . $e->getMessage());
        }
    }

    public function saveAccount()
    {
        Log::info('Pre-validation bank account data', [
            'account_name' => $this->account_name,
            'account_number' => $this->account_number,
            'editMode' => $this->editMode
        ]);

        $this->validate([
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'branch' => 'nullable|string|max:255',
            'currency' => 'required|string|size:3',
            'initial_balance' => 'required|numeric|min:0',
        ]);

        try {
            $accountData = [
                'account_name' => $this->account_name,
                'account_number' => $this->account_number,
                'bank_name' => $this->bank_name,
                'branch' => $this->branch,
                'currency' => $this->currency,
                'initial_balance' => $this->initial_balance,
            ];
            
            // For editing, don't update the initial_balance again
            if ($this->editMode) {
                unset($accountData['initial_balance']);
            }

            DB::beginTransaction();

            if ($this->editMode) {
                Log::info('Updating bank account', [
                    'account_id' => $this->accountId,
                    'account_data' => $accountData
                ]);

                $account = BankAccount::findOrFail($this->accountId);
                $account->update($accountData);

                Log::info('Bank account updated successfully', [
                    'account_id' => $account->id,
                    'account_name' => $account->account_name
                ]);

                session()->flash('message', 'Bank account updated successfully!');
            } else {
                Log::info('Creating new bank account', [
                    'account_data' => $accountData
                ]);

                // Set current_balance to initial_balance for new accounts
                $accountData['current_balance'] = $accountData['initial_balance'];
                
                $account = BankAccount::create($accountData);
                
                // Create an initial balance transaction if initial_balance > 0
                if ($accountData['initial_balance'] > 0) {
                    BankTransaction::create([
                        'bank_account_id' => $account->id,
                        'amount' => $accountData['initial_balance'],
                        'transaction_date' => now(),
                        'transaction_type' => 'deposit',
                        'description' => 'Initial balance',
                        'reference_number' => 'INIT-' . uniqid(),
                    ]);
                }

                Log::info('Bank account created successfully', [
                    'account_id' => $account->id,
                    'account_name' => $account->account_name
                ]);

                session()->flash('message', 'Bank account created successfully!');
            }
            
            DB::commit();
            
            $this->showAccountFormModal = false;
            $this->reset(['account_name', 'account_number', 'bank_name', 'branch', 'initial_balance', 'accountId', 'editMode']);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error saving bank account', [
                'mode' => $this->editMode ? 'edit' : 'create',
                'account_id' => $this->editMode ? $this->accountId : null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            session()->flash('error', 'Error saving bank account: ' . $e->getMessage());
        }
    }

    public function confirmDelete($accountId)
    {
        $this->accountToDelete = BankAccount::findOrFail($accountId);
        $this->showDeleteConfirmModal = true;
        
        Log::info('Confirming delete for bank account', [
            'account_id' => $accountId,
            'account_name' => $this->accountToDelete->account_name
        ]);
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
            
            $accountName = $this->accountToDelete->account_name;
            
            $this->accountToDelete->delete();
            
            Log::info('Bank account deleted successfully', [
                'account_id' => $this->accountToDelete->id,
                'account_name' => $accountName
            ]);
            
            session()->flash('message', 'Bank account deleted successfully!');
            
        } catch (\Exception $e) {
            Log::error('Error deleting bank account', [
                'account_id' => $this->accountToDelete ? $this->accountToDelete->id : null,
                'error' => $e->getMessage()
            ]);
            
            session()->flash('error', 'Error deleting bank account: ' . $e->getMessage());
        }
        
        $this->showDeleteConfirmModal = false;
        $this->accountToDelete = null;
    }
    
    public function viewAccountDetails($accountId)
    {
        try {
            $this->selectedAccount = BankAccount::findOrFail($accountId);
            $this->showDetailsModal = true;
            
            Log::info('Viewing bank account details', [
                'account_id' => $accountId,
                'account_name' => $this->selectedAccount->account_name
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading bank account details', [
                'account_id' => $accountId,
                'error' => $e->getMessage()
            ]);
            
            session()->flash('error', 'Error loading bank account details: ' . $e->getMessage());
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
            
            Log::info('Opening transaction form for account', [
                'account_id' => $accountId,
                'account_name' => $this->selectedAccount->account_name
            ]);
        } catch (\Exception $e) {
            Log::error('Error opening transaction form', [
                'account_id' => $accountId,
                'error' => $e->getMessage()
            ]);
            
            session()->flash('error', 'Error opening transaction form: ' . $e->getMessage());
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
            
            Log::info('Transaction created successfully', [
                'transaction_id' => $transaction->id,
                'account_id' => $this->selectedAccount->id,
                'amount' => $amount,
                'type' => $this->transaction_type,
                'new_balance' => $this->selectedAccount->current_balance
            ]);
            
            session()->flash('message', 'Transaction recorded successfully!');
            $this->showTransactionFormModal = false;
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error creating transaction', [
                'account_id' => $this->selectedAccount->id,
                'error' => $e->getMessage()
            ]);
            
            session()->flash('error', 'Error creating transaction: ' . $e->getMessage());
        }
    }
    
    public function updatedDateRange()
    {
        Log::info('Date range updated', $this->dateRange);
    }
    
    public function resetFilters()
    {
        $this->reset(['search', 'dateRange', 'transactionTypeFilter']);
        
        // Set date range to current month
        $this->dateRange['start'] = now()->startOfMonth()->format('Y-m-d');
        $this->dateRange['end'] = now()->endOfMonth()->format('Y-m-d');
        
        Log::info('Filters reset to default');
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        Log::info('Sorting bank accounts', [
            'field' => $this->sortField,
            'direction' => $this->sortDirection
        ]);
    }

    #[Computed]
    public function bankAccounts()
    {
        return BankAccount::when($this->search, function ($query) {
                $query->where(function($q) {
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
        
        return BankTransaction::where('bank_account_id', $this->selectedAccount->id)
            ->when($this->dateRange['start'] && $this->dateRange['end'], function($query) {
                $query->whereBetween('transaction_date', [
                    $this->dateRange['start'], 
                    $this->dateRange['end']
                ]);
            })
            ->when($this->transactionTypeFilter, function($query) {
                $query->where('transaction_type', $this->transactionTypeFilter);
            })
            ->orderBy('transaction_date', 'desc')
            ->paginate(10);
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
        
        $dateQuery = $this->selectedAccount->transactions();
        
        if ($this->dateRange['start'] && $this->dateRange['end']) {
            $dateQuery = $dateQuery->whereBetween('transaction_date', [
                $this->dateRange['start'], 
                $this->dateRange['end']
            ]);
        }
        
        $incoming = $dateQuery->where('amount', '>', 0)->sum('amount');
        $outgoing = $dateQuery->where('amount', '<', 0)->sum('amount');
        
        return [
            'incoming' => $incoming,
            'outgoing' => abs($outgoing),
            'net' => $incoming + $outgoing,
        ];
    }

    public function cancelAccountForm()
    {
        $this->showAccountFormModal = false;
        $this->reset(['account_name', 'account_number', 'bank_name', 'branch', 'initial_balance', 'currency', 'accountId', 'editMode']);
        Log::info('Bank account form canceled');
    }
    
    public function render()
    {
        return view('livewire.bank-accounts');
    }
}