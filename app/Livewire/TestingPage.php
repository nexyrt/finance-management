<?php

namespace App\Livewire;

use Livewire\Component;

class TestingPage extends Component
{
    public $amount = 70000; // Default value for the currency input

    public function save()
    {
        dd("". $this->amount ."");
    }

    public function render()
    {
        return view('livewire.testing-page');
    }
}
