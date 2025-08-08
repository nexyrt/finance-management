<?php

namespace App\Livewire\BankAccounts;

use Livewire\Component;

class Index extends Component
{
    public $accountId = null; 

    public function render()
    {
        return view('livewire.bank-accounts.index');
    }
}
