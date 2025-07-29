<?php

namespace App\Livewire;

use App\Models\Client;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class TestingPage extends Component
{
    use Interactions;

    // Client form properties
    public $client_type = 'individual'; // individual or company
    public $name = '';
    public $email = '';
    public $NPWP = '';
    public $KPP = '';
    public $EFIN = '';
    public $account_representative = '';
    public $ar_phone_number = '';
    public $person_in_charge = '';
    public $address = '';
    public $status = 'Active';

    public function rules()
    {
        return [
            'client_type' => 'required|in:individual,company',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:clients,email',
            'NPWP' => 'nullable|string|max:20',
            'KPP' => 'nullable|string|max:255',
            'EFIN' => 'nullable|string|max:255',
            'account_representative' => 'nullable|string|max:255',
            'ar_phone_number' => 'nullable|string|max:20',
            'person_in_charge' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'status' => 'required|in:Active,Inactive',
        ];
    }

    public function submit()
    {
        $this->validate();

        // Clean and format data
        $this->cleanFormData();

        try {
            $clientData = [
                'type' => $this->client_type,
                'name' => $this->name,
                'email' => $this->email,
                'NPWP' => $this->NPWP,
                'KPP' => $this->KPP,
                'EFIN' => $this->EFIN,
                'account_representative' => $this->account_representative,
                'ar_phone_number' => $this->ar_phone_number,
                'person_in_charge' => $this->person_in_charge,
                'address' => $this->address,
                'status' => $this->status,
            ];

            // Create client
            Client::create($clientData);

            $this->dialog()->success('Berhasil!', 'Client berhasil disimpan!')->send();
            $this->resetForm();

        } catch (\Exception $e) {
            $this->dialog()->error('Gagal!', 'Gagal menyimpan client: ' . $e->getMessage())->send();
        }
    }

    private function cleanFormData()
    {
        // Clean NPWP (remove non-alphanumeric characters)
        if ($this->NPWP) {
            $this->NPWP = preg_replace('/[^\d\.]/', '', $this->NPWP);
        }
        
        // Clean phone number (remove non-digits)
        if ($this->ar_phone_number) {
            $this->ar_phone_number = preg_replace('/[^\d]/', '', $this->ar_phone_number);
        }
    }

    public function resetForm()
    {
        $this->reset([
            'name', 'email', 'NPWP', 'KPP', 'EFIN', 'account_representative', 
            'ar_phone_number', 'person_in_charge', 'address'
        ]);
        $this->client_type = 'individual';
        $this->status = 'Active';
    }

    public function render()
    {
        return view('livewire.testing-page');
    }
}
