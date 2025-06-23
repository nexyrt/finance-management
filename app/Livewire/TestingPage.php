<?php

namespace App\Livewire;

use App\Models\BankAccount;
use Livewire\Component;

class TestingPage extends Component
{
    // Bank Account form properties
    public $account_name = '';
    public $account_number = '';
    public $bank_name = '';
    public $branch = '';
    public $initial_balance = 0;
    public $current_balance = 0;

    protected $rules = [
        'account_name' => 'required|string|max:255',
        'account_number' => 'required|string|max:255',
        'bank_name' => 'required|string|max:255',
        'branch' => 'nullable|string|max:255',
        'initial_balance' => 'required|numeric|min:0',
        'current_balance' => 'required|numeric|min:0',
    ];

    public function updatedInitialBalance($value)
    {
        // Auto-set current balance to initial balance if current balance is 0
        if ($this->current_balance == 0) {
            $this->current_balance = $value;
        }
    }

    public function save()
    {
        $this->validate();

        try {
            $bankAccount = BankAccount::create([
                'account_name' => $this->account_name,
                'account_number' => $this->account_number,
                'bank_name' => $this->bank_name,
                'branch' => $this->branch ?: null,
                'initial_balance' => $this->initial_balance,
                'current_balance' => $this->current_balance,
            ]);

            session()->flash('success', 
                'Bank Account berhasil dibuat! ' . $this->account_name . 
                ' (' . $this->bank_name . ') - Balance: Rp ' . number_format($this->current_balance, 0, ',', '.')
            );
            
            $this->resetForm();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function setTestData()
    {
        $this->account_name = 'PT ABC Company';
        $this->account_number = '1234567890';
        $this->bank_name = 'Bank Central Asia';
        $this->branch = 'Jakarta Pusat';
        $this->initial_balance = 50000000; // 50 juta
        $this->current_balance = 50000000;
    }

    public function resetForm()
    {
        $this->reset(['account_name', 'account_number', 'bank_name', 'branch', 'initial_balance', 'current_balance']);
    }

    public function getBankAccountsProperty()
    {
        return BankAccount::latest()->take(5)->get();
    }

    public function render()
    {
        return view('livewire.testing-page', [
            'bankAccounts' => $this->bankAccounts
        ]);
    }
}