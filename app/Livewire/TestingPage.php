<?php

namespace App\Livewire;

use App\Models\Client;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class TestingPage extends Component
{
    use Interactions;

    public $salary;

    public function submit()
    {
        $this->dialog()->success('Success', 'Your salary has been submitted successfully.')->send();
    }

    public function render()
    {
        return view('livewire.testing-page');
    }
}
