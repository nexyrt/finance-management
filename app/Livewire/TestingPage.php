<?php

namespace App\Livewire;

use App\Services\GeminiFinanceService;
use Livewire\Component;
use Illuminate\Support\Facades\RateLimiter;

class TestingPage extends Component
{
    // Test x-currency-input component
    public $amount1 = 123456;
    public $amount2 = 880000;
    public $amount3 = 123456;

    public function render()
    {
        return view('livewire.testing-page');
    }
}
