<?php

namespace App\Livewire;

use App\Models\Client;
use Livewire\WithPagination;
use Livewire\Component;

class TestingPage extends Component
{
    use WithPagination;
    
    public $selected = [];

    public function deleteSelected()
    {
        Client::destroy($this->selected);
        $this->selected = [];
        session()->flash('message', 'Selected clients deleted successfully.');
    }

    public function render()
    {
        $clients = Client::paginate(10);

        return view('livewire.testing-page', [
            'clients' => $clients,
        ]);
    }
}
