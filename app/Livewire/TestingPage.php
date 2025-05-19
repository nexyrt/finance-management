<?php

namespace App\Livewire;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class TestingPage extends Component
{
    public function render()
    {
        return view('livewire.testing-page', [
            'accounts' => BankAccount::paginate(15),
            'transactions' => BankTransaction::where('bank_account_id', 3)->paginate(15),
        ]);
    }
}
