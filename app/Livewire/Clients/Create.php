<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Create extends Component
{
    use Interactions;

    public bool $modal = false;

    // Form properties
    public string $name = '';
    public string $type = 'individual';
    public string $email = '';
    public string $NPWP = '';
    public string $KPP = '';
    public string $EFIN = '';
    public string $status = 'Active';
    public string $account_representative = '';
    public string $ar_phone_number = '';
    public string $person_in_charge = '';
    public string $address = '';

    protected array $rules = [
        'name' => 'required|string|max:255',
        'type' => 'required|in:individual,company',
        'email' => 'nullable|email|unique:clients,email',
        'NPWP' => 'nullable|string|max:20',
        'KPP' => 'nullable|string|max:20',
        'EFIN' => 'nullable|string|max:20',
        'status' => 'required|in:Active,Inactive',
        'account_representative' => 'nullable|string|max:255',
        'ar_phone_number' => 'nullable|string|max:20',
        'person_in_charge' => 'nullable|string|max:255',
        'address' => 'nullable|string',
    ];

    public function save()
    {
        $this->validate();

        $client = Client::create([
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

        $this->modal = false;
        $this->dispatch('client-created');
        $this->toast()->success("{$client->name} created successfully.")->send();
        $this->resetForm();
    }

    public function close()
    {
        $this->modal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    private function resetForm()
    {
        $this->reset([
            'name', 'type', 'email', 'NPWP', 'KPP', 'EFIN', 
            'status', 'account_representative', 'ar_phone_number', 
            'person_in_charge', 'address'
        ]);
        $this->type = 'individual';
        $this->status = 'Active';
    }
}