<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Edit extends Component
{
    use Interactions;

    public Client $client;
    public bool $showEditModal = false;
    
    // Form properties
    public string $name = '';
    public string $type = '';
    public string $email = '';
    public string $NPWP = '';
    public string $status = '';
    public string $account_representative = '';
    public string $address = '';

    protected array $rules = [
        'name' => 'required|string|max:255',
        'type' => 'required|in:individual,company',
        'email' => 'nullable|email',
        'NPWP' => 'nullable|string',
        'status' => 'required|in:Active,Inactive',
        'account_representative' => 'nullable|string',
        'address' => 'nullable|string',
    ];

    public function openEditModal()
    {
        $this->name = $this->client->name;
        $this->type = $this->client->type;
        $this->email = $this->client->email ?? '';
        $this->NPWP = $this->client->NPWP ?? '';
        $this->status = $this->client->status;
        $this->account_representative = $this->client->account_representative ?? '';
        $this->address = $this->client->address ?? '';
        $this->showEditModal = true;
    }

    public function updateClient()
    {
        $this->validate();

        $this->client->update([
            'name' => $this->name,
            'type' => $this->type,
            'email' => $this->email,
            'NPWP' => $this->NPWP,
            'status' => $this->status,
            'account_representative' => $this->account_representative,
            'address' => $this->address,
        ]);

        $this->showEditModal = false;
        $this->dispatch('client-updated');
        $this->toast()->success("{$this->client->name} updated successfully.")->send();
    }
}