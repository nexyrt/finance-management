<?php

namespace App\Livewire;

use Livewire\Component;

class TestingPage extends Component
{
    public bool $myModal1 = false;
    public $money2 = 78000;

    public function save()
    {
        dd('Save method called with money2: ' . $this->money2);
    }

    public function render()
    {
        return view('livewire.testing-page', [
            'users' => [
                ['id' => 1, 'name' => 'John Doe'],
                ['id' => 2, 'name' => 'Jane Smith'],
                ['id' => 3, 'name' => 'Alice Johnson'],
                ['id' => 4, 'name' => 'Bob Brown'],
            ]
        ]);
    }
}
