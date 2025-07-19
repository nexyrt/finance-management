<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Delete extends Component
{
    use Interactions;

    public Client $client;
    public bool $showDeleteModal = false;

    public function deleteClient()
    {
        $clientName = $this->client->name;
        $this->client->delete();
        $this->showDeleteModal = false;
        $this->dispatch('client-deleted');
        $this->toast()->success("{$clientName} deleted successfully.")->send();
    }
}