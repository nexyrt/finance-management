<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Show extends Component
{
    use Interactions;

    public Client $client;
    public bool $showModal = false;

    public function mount(Client $client)
    {
        $this->client = $client->load(['invoices', 'owners', 'ownedCompanies']);
    }

    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function editClient()
    {
        $this->showModal = false; // Close show modal first
        $this->dispatch('open-client-edit', $this->client->id);
    }

    // Helper methods for calculations
    public function getTotalInvoices()
    {
        return $this->client->invoices->count();
    }

    public function getTotalAmount()
    {
        return $this->client->invoices->sum('total_amount');
    }

    public function getPaidAmount()
    {
        return $this->client->invoices->where('status', 'paid')->sum('total_amount');
    }

    public function getOutstandingAmount()
    {
        return $this->getTotalAmount() - $this->getPaidAmount();
    }

    public function getRecentInvoices()
    {
        return $this->client->invoices()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }
}