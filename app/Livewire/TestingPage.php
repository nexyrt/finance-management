<?php

namespace App\Livewire;

use Livewire\Component;

class TestingPage extends Component
{
    public $amount = '75000'; // Default value for the currency input
    public $price = '0'; // Default value for the currency input

    public function submit()
    {
        $amountInt = (int) $this->amount;
        $priceInt = (int) $this->price;
        $result = $amountInt - $priceInt;

        dd($result, gettype($result));
    }

    public function render()
    {
        return view('livewire.testing-page');
    }
}
