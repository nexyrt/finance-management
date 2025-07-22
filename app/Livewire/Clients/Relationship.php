<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use App\Models\ClientRelationship;
use Livewire\Attributes\On;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Relationship extends Component
{
    use Interactions;

    public ?Client $client = null;
    public bool $showModal = false;
    public string $relationshipType = 'owner'; // 'owner' or 'company'

    // Form properties
    public $selectedClients = [];
    public $availableClients = [];

    #[On('manage-relationships')]
    public function manage($clientId)
    {
        $this->client = Client::with(['owners', 'ownedCompanies'])->find($clientId);
        
        if ($this->client) {
            $this->loadAvailableClients();
            $this->loadExistingRelationships();
            $this->showModal = true;
        }
    }

    private function loadAvailableClients()
    {
        if ($this->client->type === 'individual') {
            // For individuals, show available companies
            $this->relationshipType = 'company';
            $this->availableClients = Client::where('type', 'company')
                ->where('id', '!=', $this->client->id)
                ->where('status', 'Active')
                ->get()
                ->map(fn($client) => [
                    'label' => $client->name,
                    'value' => $client->id
                ])->toArray();
        } else {
            // For companies, show available individuals
            $this->relationshipType = 'owner';
            $this->availableClients = Client::where('type', 'individual')
                ->where('id', '!=', $this->client->id)
                ->where('status', 'Active')
                ->get()
                ->map(fn($client) => [
                    'label' => $client->name,
                    'value' => $client->id
                ])->toArray();
        }
    }

    private function loadExistingRelationships()
    {
        if ($this->client->type === 'individual') {
            $this->selectedClients = $this->client->ownedCompanies->pluck('id')->toArray();
        } else {
            $this->selectedClients = $this->client->owners->pluck('id')->toArray();
        }
    }

    public function save()
    {
        if ($this->client->type === 'individual') {
            // Sync owned companies
            $this->client->ownedCompanies()->sync($this->selectedClients);
            $message = 'Company relationships updated';
        } else {
            // Sync owners
            $this->client->owners()->sync($this->selectedClients);
            $message = 'Owner relationships updated';
        }

        $this->close();
        $this->dispatch('relationships-updated');
        $this->toast()->success($message)->send();
    }

    public function close()
    {
        $this->showModal = false;
        $this->client = null;
        $this->selectedClients = [];
        $this->availableClients = [];
    }
}