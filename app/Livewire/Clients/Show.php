<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Livewire\Attributes\On;
use Livewire\Component;

class Show extends Component
{
    public ?Client $client = null;
    public bool $showModal = false;

    #[On('show-client')]
    public function show($clientId)
    {
        $this->client = Client::with([
            'invoices' => fn($q) => $q->latest()->limit(5),
            'owners',
            'ownedCompanies'
        ])->find($clientId);

        if ($this->client) {
            $this->showModal = true;
        }
    }

    public function close()
    {
        $this->showModal = false;
        $this->client = null;
    }

    public function editClient()
    {
        if ($this->client) {
            $clientId = $this->client->id;
            $this->close();
            $this->dispatch('edit-client', clientId: $clientId);
        }
    }

    public function manageRelationships()
    {
        if ($this->client) {
            $clientId = $this->client->id;
            $this->close();
            $this->dispatch('manage-relationships', clientId: $clientId);
        }
    }

    // Computed properties
    public function getTotalInvoices()
    {
        return $this->client?->invoices()->count() ?? 0;
    }

    public function getTotalAmount()
    {
        return $this->client?->invoices()->sum('total_amount') ?? 0;
    }

    public function getPaidAmount()
    {
        return $this->client?->invoices()->where('status', 'paid')->sum('total_amount') ?? 0;
    }

    public function getOutstandingAmount()
    {
        return $this->getTotalAmount() - $this->getPaidAmount();
    }
}