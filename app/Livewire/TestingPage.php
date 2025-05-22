<?php

namespace App\Livewire;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use Flux\Flux;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class TestingPage extends Component
{
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

    protected $rules = [
        'form.account_name' => 'required|string|max:255',
        'form.account_number' => 'required|string|max:50',
        'form.bank_name' => 'required|string|max:255',
        'form.branch' => 'nullable|string|max:255',
        'form.currency' => 'required|string|in:IDR,USD,EUR,SGD',
        'form.initial_balance' => 'required|numeric|min:0',
    ];

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

    public function render()
    {
        return view('livewire.testing-page', [
            'accounts' => BankAccount::all(),
            'transactions' => BankTransaction::where('bank_account_id', 3)->paginate(15),
        ]);
    }
}
