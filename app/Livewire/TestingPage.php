<?php

namespace App\Livewire;

use Livewire\Component;
use TallStackUi\Traits\Interactions;

class TestingPage extends Component
{
    use Interactions;

    public function render()
    {
        return view('livewire.testing-page');
    }
}