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
    public bool $clientEditModal = false;

    // Form properties
    public string $name = '';
    public string $type = '';
    public string $email = '';
    public string $NPWP = '';
    public string $KPP = '';
    public string $EFIN = '';
    public string $status = '';
    public string $account_representative = '';
    public string $ar_phone_number = '';
    public string $person_in_charge = '';
    public string $address = '';

    protected array $rules = [
        'name' => 'required|string|max:255',
        'type' => 'required|in:individual,company',
        'email' => 'nullable|email',
        'NPWP' => 'nullable|string|max:20',
        'KPP' => 'nullable|string|max:20',
        'EFIN' => 'nullable|string|max:20',
        'status' => 'required|in:Active,Inactive',
        'account_representative' => 'nullable|string|max:255',
        'ar_phone_number' => 'nullable|string|max:20',
        'person_in_charge' => 'nullable|string|max:255',
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
            'KPP' => $this->client->KPP ?? '',
            'EFIN' => $this->client->EFIN ?? '',
            'status' => $this->client->status,
            'account_representative' => $this->client->account_representative ?? '',
            'ar_phone_number' => $this->client->ar_phone_number ?? '',
            'person_in_charge' => $this->client->person_in_charge ?? '',
            'address' => $this->client->address ?? '',
        ]);

        $this->clientEditModal = true;
    }

    public function save()
    {
        $this->validate();

        $this->client->update([
            'name' => $this->name,
            'type' => $this->type,
            'email' => $this->email ?: null,
            'NPWP' => $this->NPWP ?: null,
            'KPP' => $this->KPP ?: null,
            'EFIN' => $this->EFIN ?: null,
            'status' => $this->status,
            'account_representative' => $this->account_representative ?: null,
            'ar_phone_number' => $this->ar_phone_number ?: null,
            'person_in_charge' => $this->person_in_charge ?: null,
            'address' => $this->address ?: null,
        ]);

        $this->clientEditModal = false;
        $this->dispatch('client-updated');
        $this->toast()->success(__('common.success'), __('common.updated_successfully'))->send();
        $this->reset();
    }
}