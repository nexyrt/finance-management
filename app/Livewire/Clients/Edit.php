<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Edit extends Component
{
    use Interactions;

    public ?Client $client = null;
    public bool $showEditModal = false;

    // Form properties
    public string $name = '';
    public string $type = '';
    public string $email = '';
    public string $NPWP = '';
    public string $status = '';
    public string $account_representative = '';
    public string $address = '';

    protected $listeners = [
        'open-client-edit' => 'openEditFromGlobal'
    ];

    protected array $rules = [
        'name' => 'required|string|max:255',
        'type' => 'required|in:individual,company',
        'email' => 'nullable|email',
        'NPWP' => 'nullable|string',
        'status' => 'required|in:Active,Inactive',
        'account_representative' => 'nullable|string',
        'address' => 'nullable|string',
    ];

    public function mount(?Client $client = null)
    {
        if ($client) {
            $this->client = $client;
            $this->loadClientData();
        }
    }

    // Method untuk membuka edit dari dalam dropdown (existing)
    public function openEditModal()
    {
        $this->loadClientData();
        $this->showEditModal = true;
    }

    // Method untuk membuka edit dari global event (NEW)
    public function openEditFromGlobal($clientId)
    {
        $this->client = Client::find($clientId);
        $this->loadClientData();
        $this->showEditModal = true;
    }

    private function loadClientData()
    {
        if (!$this->client) return;

        $this->name = $this->client->name;
        $this->type = $this->client->type;
        $this->email = $this->client->email ?? '';
        $this->NPWP = $this->client->NPWP ?? '';
        $this->status = $this->client->status;
        $this->account_representative = $this->client->account_representative ?? '';
        $this->address = $this->client->address ?? '';
    }

    public function updateClient()
    {
        $this->validate();

        $this->client->update([
            'name' => $this->name,
            'type' => $this->type,
            'email' => $this->email ?: null,
            'NPWP' => $this->NPWP ?: null,
            'status' => $this->status,
            'account_representative' => $this->account_representative ?: null,
            'address' => $this->address ?: null,
        ]);

        $this->showEditModal = false;
        $this->dispatch('client-updated');
        $this->toast()->success("{$this->client->name} updated successfully.")->send();
    }

    public function closeModal()
    {
        $this->showEditModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset(['name', 'type', 'email', 'NPWP', 'status', 'account_representative', 'address']);
        $this->resetValidation();
    }
}