<?php

namespace App\Livewire;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use Flux\Flux;
use Livewire\Component;
use Masmerise\Toaster\Toast;
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

    protected $rules = [
        'form.account_name' => 'required|string|max:255',
        'form.account_number' => 'required|string|max:50',
        'form.bank_name' => 'required|string|max:255',
        'form.branch' => 'nullable|string|max:255',
        'form.currency' => 'required|string|in:IDR,USD,EUR,SGD',
        'form.initial_balance' => 'required|numeric|min:0',
    ];

    public function saveBankAccount()
    {
        $this->validate();

        // Set current balance equal to initial balance for new accounts
        $this->form['current_balance'] = $this->form['initial_balance'];

        // Create bank account
        BankAccount::create($this->form);

        // Reset form and close modal
        $this->reset('form');   
        Flux::modals()->close();

        // Show success message
        Toaster::success('Bank account created successfully!');
    }

    public function render()
    {
        return view('livewire.testing-page', [
            'accounts' => BankAccount::all(),
            'transactions' => BankTransaction::where('bank_account_id', 3)->paginate(15),
        ]);
    }
}
