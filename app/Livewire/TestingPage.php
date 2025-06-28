<?php
// app/Livewire/TestingPage.php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Service;
use App\Models\Client;
use App\Models\BankAccount;

class TestingPage extends Component
{
    // Form properties
    public $selectedService = '';
    public $selectedClient = '';
    public $selectedBank = '';
    public $selectedCity = '';
    
    // Static data
    public $cities = [
        ['id' => 'jakarta', 'name' => 'Jakarta'],
        ['id' => 'surabaya', 'name' => 'Surabaya'],
        ['id' => 'bandung', 'name' => 'Bandung'],
        ['id' => 'medan', 'name' => 'Medan'],
        ['id' => 'semarang', 'name' => 'Semarang'],
        ['id' => 'palembang', 'name' => 'Palembang'],
        ['id' => 'makassar', 'name' => 'Makassar'],
        ['id' => 'balikpapan', 'name' => 'Balikpapan'],
        ['id' => 'yogyakarta', 'name' => 'Yogyakarta'],
        ['id' => 'malang', 'name' => 'Malang'],
    ];
    
    public function mount()
    {
        // Set default values for testing
        $this->selectedCity = 'Jakarta';
    }
    
    public function updatedSelectedService($value)
    {
        session()->flash('message', 'Service updated: ' . $value);
    }
    
    public function updatedSelectedClient($value)
    {
        session()->flash('message', 'Client updated: ' . $value);
    }
    
    public function updatedSelectedBank($value)
    {
        session()->flash('message', 'Bank updated: ' . $value);
    }
    
    public function updatedSelectedCity($value)
    {
        session()->flash('message', 'City updated: ' . $value);
    }
    
    public function submitForm()
    {
        $this->validate([
            'selectedService' => 'required',
            'selectedClient' => 'required',
            'selectedBank' => 'required',
            'selectedCity' => 'required',
        ]);
        
        session()->flash('success', 'Form submitted successfully!');
        
        // Reset form
        $this->reset(['selectedService', 'selectedClient', 'selectedBank']);
        
        // Dispatch event to reinitialize dropdowns after reset
        $this->dispatch('form-reset');
    }
    
    public function render()
    {
        return view('livewire.testing-page', [
            'services' => Service::all(),
            'clients' => Client::all(),
            'bankAccounts' => BankAccount::all(),
        ]);
    }
}