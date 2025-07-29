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
    public $phone = '';
    public $tax_id = '';
    public $birth_date = '';
    public $salary = '';
    public $credit_card = '';
    
    // Address
    public $address = '';
    public $city = '';
    public $postal_code = '';
    public $country = 'Indonesia';

    // Company specific (if client_type is company)
    public $company_name = '';
    public $company_registration = '';
    public $website = '';

    public function rules()
    {
        $rules = [
            'client_type' => 'required|in:individual,company',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'country' => 'required|string|max:100',
        ];

        if ($this->client_type === 'individual') {
            $rules['birth_date'] = 'nullable|date';
            $rules['tax_id'] = 'nullable|string|max:20';
        } else {
            $rules['company_name'] = 'required|string|max:255';
            $rules['company_registration'] = 'required|string|max:50';
            $rules['tax_id'] = 'required|string|max:20';
            $rules['website'] = 'nullable|url|max:255';
        }

        return $rules;
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
                'phone' => $this->phone,
                'tax_id' => $this->tax_id,
                'address' => $this->address,
                'city' => $this->city,
                'postal_code' => $this->postal_code,
                'country' => $this->country,
            ];

            if ($this->client_type === 'individual') {
                $clientData['birth_date'] = $this->birth_date ? \Carbon\Carbon::createFromFormat('d/m/Y', $this->birth_date)->format('Y-m-d') : null;
            } else {
                $clientData['company_name'] = $this->company_name;
                $clientData['company_registration'] = $this->company_registration;
                $clientData['website'] = $this->website;
            }

            // Create client (uncomment when Client model is ready)
            // Client::create($clientData);

            $this->toast()->success('Client berhasil disimpan!')->send();
            $this->resetForm();

            // For testing - show the cleaned data
            dd($clientData);

        } catch (\Exception $e) {
            $this->toast()->error('Gagal menyimpan client: ' . $e->getMessage())->send();
        }
    }

    private function cleanFormData()
    {
        // Clean phone number (remove non-digits)
        $this->phone = preg_replace('/[^\d]/', '', $this->phone);
        
        // Clean postal code (remove non-digits)
        $this->postal_code = preg_replace('/[^\d]/', '', $this->postal_code);
        
        // Clean tax ID (remove non-alphanumeric)
        $this->tax_id = preg_replace('/[^\w]/', '', $this->tax_id);
        
        // Clean salary (remove non-digits)
        if ($this->salary) {
            $this->salary = preg_replace('/[^\d]/', '', $this->salary);
            $this->salary = (int) $this->salary;
        }
        
        // Clean credit card (remove non-digits)
        if ($this->credit_card) {
            $this->credit_card = preg_replace('/[^\d]/', '', $this->credit_card);
        }
        
        // Clean company registration (remove non-alphanumeric)
        if ($this->company_registration) {
            $this->company_registration = preg_replace('/[^\w]/', '', $this->company_registration);
        }
    }

    public function resetForm()
    {
        $this->reset([
            'name', 'email', 'phone', 'tax_id', 'birth_date', 'salary', 'credit_card',
            'address', 'city', 'postal_code', 'company_name', 'company_registration', 'website'
        ]);
        $this->client_type = 'individual';
        $this->country = 'Indonesia';
    }

    public function render()
    {
        return view('livewire.testing-page');
    }
}
