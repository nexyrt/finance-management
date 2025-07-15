<?php

namespace App\Livewire;

use Livewire\Component;

class TestingPage extends Component
{
    public function render()
    {
        return view('livewire.testing-page', [
            'users' => [
                ['value' => 1, 'label' => 'John Doe'],
                ['value' => 2, 'label' => 'Jane Smith'],
                ['value' => 3, 'label' => 'Alice Johnson'],
                ['value' => 4, 'label' => 'Bob Brown'],
            ]
        ]);
    }
}
