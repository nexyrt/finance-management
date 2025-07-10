<?php

namespace App\Livewire;

use Livewire\Component;

class TestingPage extends Component
{
    public $cleaveAmount = 150000;
    public $intlAmount = 150000;

    public function submit()
    {
        // Cek hasil input
        dd([
            'cleave' => $this->cleaveAmount,
            'intl' => $this->intlAmount,
        ]);
    }

    public function render()
    {
        return view('livewire.testing-page');
    }
}
