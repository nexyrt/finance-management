<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Livewire\Attributes\On;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Delete extends Component
{
    use Interactions;

    public ?Client $client = null;
    public bool $showDeleteModal = false;

    #[On('delete-client')]
    public function delete($clientId)
    {
        $this->client = Client::with(['invoices' => function($query) {
            $query->select('id', 'billed_to_id', 'invoice_number', 'total_amount', 'status', 'issue_date');
        }])->find($clientId);
        
        if ($this->client) {
            $this->showDeleteModal = true;
        }
    }

    public function confirm()
    {
        if ($this->client) {
            $name = $this->client->name;
            $this->client->delete();
            $this->close();
            $this->dispatch('client-deleted');
            $this->toast()->success("{$name} deleted successfully.")->send();
        }
    }

    public function close()
    {
        $this->showDeleteModal = false;
        $this->client = null;
    }

    public function render()
    {
        return view('livewire.clients.delete');
    }
}