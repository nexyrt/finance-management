<?php

namespace App\Livewire;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;
use Masmerise\Toaster\Toaster;
use Carbon\Carbon;

class TestingPage extends Component
{
    use WithPagination;

    public $form = [
        'account_name' => '',
        'account_number' => '',
        'bank_name' => '',
        'branch' => '',
        'currency' => 'IDR',
        'initial_balance' => '',
        'current_balance' => 0,
    ];
    public $date;
    public $editMode = false;
    public $editId = null;
    public $selectedBankId = null;
    public $dateRange = '';
    public $startDate = null;
    public $endDate = null;
    public $transactionType = '';

    protected $rules = [
        'form.account_name' => 'required|string|max:255',
        'form.account_number' => 'required|string|max:50',
        'form.bank_name' => 'required|string|max:255',
        'form.branch' => 'nullable|string|max:255',
        'form.currency' => 'required|string|in:IDR,USD,EUR,SGD',
        'form.initial_balance' => 'required|numeric|min:0',
    ];

    protected $queryString = [
        'selectedBankId' => ['except' => null],
        'startDate' => ['except' => null], 
        'endDate' => ['except' => null],
        'transactionType' => ['except' => ''],
    ];

    /**
     * Initialize component
     */
    public function mount()
    {
        // Format any existing date range values when component loads
        if ($this->startDate && $this->endDate) {
            try {
                $startFormatted = Carbon::parse($this->startDate)->format('d/m/Y');
                $endFormatted = Carbon::parse($this->endDate)->format('d/m/Y');
                $this->dateRange = "$startFormatted - $endFormatted";
            } catch (\Exception $e) {
                // Handle invalid dates silently
                $this->startDate = null;
                $this->endDate = null;
                $this->dateRange = '';
            }
        }
    }

    public function resetForm()
    {
        $this->form = [
            'account_name' => '',
            'account_number' => '',
            'bank_name' => '',
            'branch' => '',
            'currency' => 'IDR',
            'initial_balance' => '',
            'current_balance' => 0,
        ];

        $this->editMode = false;
        $this->editId = null;
    }

    public function editBankAccount($id)
    {
        $this->editMode = true;
        $this->editId = $id;

        $account = BankAccount::findOrFail($id);

        $this->form = [
            'account_name' => $account->account_name,
            'account_number' => $account->account_number,
            'bank_name' => $account->bank_name,
            'branch' => $account->branch,
            'currency' => $account->currency,
            'initial_balance' => $account->initial_balance,
            'current_balance' => $account->current_balance,
        ];

        Flux::modal("add-wallet")->show();
    }

    public function saveOrUpdateBankAccount()
    {
        $this->validate();

        try {
            if ($this->editMode) {
                // Update existing account
                $account = BankAccount::findOrFail($this->editId);

                // Calculate the difference in initial balance
                $balanceDifference = $this->form['initial_balance'] - $account->initial_balance;

                // Update the account details
                $account->update([
                    'account_name' => $this->form['account_name'],
                    'account_number' => $this->form['account_number'],
                    'bank_name' => $this->form['bank_name'],
                    'branch' => $this->form['branch'],
                    'currency' => $this->form['currency'],
                    'initial_balance' => $this->form['initial_balance'],
                    // Adjust current balance by the same amount that initial balance changed
                    'current_balance' => $account->current_balance + $balanceDifference,
                ]);

                Toaster::success('Bank account updated successfully!');
            } else {
                // Create new account
                $this->form['current_balance'] = $this->form['initial_balance'];
                BankAccount::create($this->form);

                Toaster::success('Bank account created successfully!');
            }

            // Reset form and close modal
            $this->reset(['form', 'editMode', 'editId']);
            Flux::modals()->close();

        } catch (\Exception $e) {
            Toaster::error('Operation failed: ' . $e->getMessage());
        }
    }

    public function deleteBankAccount($id)
    {
        try {
            $account = BankAccount::findOrFail($id);
            $accountName = $account->account_name;

            // Delete the account
            $account->delete();
            Flux::modals()->close();

            // Show success message
            Toaster::success("Bank account \"$accountName\" has been deleted.");
        } catch (\Exception $e) {
            Toaster::error($e->getMessage());
        }
    }

    /**
     * Reset all transaction filters
     */
    public function resetFilters()
    {
        $this->dateRange = '';
        $this->startDate = null;
        $this->endDate = null;
        $this->transactionType = '';
        $this->resetPage();
    }

    /**
     * Handle date range selection
     */
    public function updatedDateRange($value)
    {
        if (empty($value)) {
            $this->startDate = null;
            $this->endDate = null;
            return;
        }

        $dates = explode(' - ', $value);
        if (count($dates) === 2) {
            try {
                // Parse date format (DD/MM/YYYY)
                $this->startDate = Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay()->toDateString();
                $this->endDate = Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay()->toDateString();
            } catch (\Exception $e) {
                $this->startDate = null;
                $this->endDate = null;
                Toaster::error('Invalid date format');
            }
        }

        $this->resetPage();
    }

    /**
     * Fetch transactions for a specific bank account
     * 
     * @param int $bankId The ID of the bank account
     * @return void
     */
    public function loadBankTransactions($bankId)
    {
        try {
            // Verify the bank exists
            $bank = BankAccount::findOrFail($bankId);

            // Set the selected bank ID
            $this->selectedBankId = $bankId;

            // Reset page for pagination
            $this->resetPage();

            // Show success message (optional)
            Toaster::success("Viewing transactions for {$bank->bank_name} - {$bank->account_name}");
        } catch (\Exception $e) {
            Toaster::error('Failed to load bank transactions: ' . $e->getMessage());
        }
    }

    /**
     * Reset selected bank account
     */
    public function clearSelectedBank()
    {
        $this->selectedBankId = null;
        $this->resetPage();
    }

    // Add this method to reset the daterangepicker
    public function resetDatepicker()
    {
        // This intentionally empty method will trigger a re-render
        // which will help resolve the datepicker display issue
    }

    public function render()
    {
        // Build the transaction query
        $transactionQuery = BankTransaction::query();

        // Filter by bank account if selected
        if ($this->selectedBankId) {
            $transactionQuery->where('bank_account_id', $this->selectedBankId);
        }

        // Filter by date range
        if ($this->startDate && $this->endDate) {
            $transactionQuery->whereBetween('transaction_date', [$this->startDate, $this->endDate]);
        }

        // Filter by transaction type
        if ($this->transactionType) {
            $transactionQuery->where('transaction_type', $this->transactionType);
        }

        // Get all accounts for the table
        $accounts = BankAccount::all();

        // Get filtered transactions with pagination
        $transactions = $transactionQuery->with('bankAccount')
            ->latest('transaction_date')
            ->paginate(10);

        return view('livewire.testing-page', [
            'accounts' => $accounts,
            'transactions' => $transactions,
        ]);
    }
}
