<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Livewire\Attributes\On;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Edit extends Component
{
    use Interactions;

    public ?Client $client = null;
    public bool $showModal = false;

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

    #[On('edit-client')]
    public function edit($clientId)
    {
        $this->client = Client::find($clientId);
        
        if (!$this->client) return;

        $this->fill([
            'name' => $this->client->name,
            'type' => $this->client->type,
            'email' => $this->client->email ?? '',
            'NPWP' => $this->client->NPWP ?? '',
            'status' => $this->client->status,
            'account_representative' => $this->client->account_representative ?? '',
            'address' => $this->client->address ?? '',
        ]);

        $this->showModal = true;
    }

    public function save()
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

        $this->showModal = false;
        $this->dispatch('client-updated');
        $this->toast()->success("{$this->client->name} updated successfully.")->send();
        $this->reset();
    }
}