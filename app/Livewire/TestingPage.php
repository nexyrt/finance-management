<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Http;
use Livewire\Component;

class TestingPage extends Component
{
    public array $accounts = [];
    public string $error = '';

    public function mount(): void
    {
        $this->loadAccounts();
    }

    public function loadAccounts(): void
    {
        $response = Http::withoutVerifying()->get('https://finance.kisantra.com/api/transaction-categories');
        $this->accounts = $response->json() ?? [];
    }

    public function render()
    {
        return view('livewire.testing-page');
    }
}
