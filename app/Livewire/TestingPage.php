<?php

namespace App\Livewire;
use Livewire\Component;

class TestingPage extends Component
{
    public $date;

    public function render()
    {
        return view('livewire.testing-page');
    }
}
