<?php

namespace App\Livewire;

use Livewire\Component;

class TestingPage extends Component
{
    public $selected = [];

    public function render()
    {
        $clients = \App\Models\Client::all();

        return view('livewire.testing-page', [
            'clients' => $clients,
        ]);
    }
}
