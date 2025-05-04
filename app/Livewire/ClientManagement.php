<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\ClientRelationship;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class ClientManagement extends Component
{
    use WithPagination;

    public $perPage = 10;
    public $search = '';
    public $typeFilter = '';
    public $selectedClients = [];
    public $selectAll = false;
    
    // Modal states
    public $showModal = false;
    public $showDeleteModal = false;
    public $editingClient = null;
    public $isEditing = false;
    
    // Form data
    public $form = [
        'name' => '',
        'type' => 'individual',
        'email' => '',
        'phone' => '',
        'address' => '',
        'tax_id' => '',
    ];
    
    // Related clients selection
    public $selectedOwner = '';
    public $selectedCompany = '';
    public $ownershipModal = false;
    public $ownershipClient = null;
    
    protected $rules = [
        'form.name' => 'required|string|max:255',
        'form.type' => 'required|in:individual,company',
        'form.email' => 'nullable|email|max:255',
        'form.phone' => 'nullable|string|max:20',
        'form.address' => 'nullable|string',
        'form.tax_id' => 'nullable|string|max:50',
    ];

    public function mount()
    {
        $this->search = '';
        $this->typeFilter = '';
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedClients = $this->getClientsProperty()->pluck('id')->toArray();
        } else {
            $this->selectedClients = [];
        }
    }

    public function updatedSelectedClients()
    {
        $this->selectAll = count($this->selectedClients) === $this->getClientsProperty()->count();
    }

    public function getClientsProperty()
    {
        return Client::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->typeFilter, function ($query) {
                $query->where('type', $this->typeFilter);
            })
            ->latest()
            ->paginate($this->perPage);
    }

    public function create()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function edit($clientId)
    {
        $client = Client::findOrFail($clientId);
        $this->editingClient = $client;
        $this->isEditing = true;
        $this->form = [
            'name' => $client->name,
            'type' => $client->type,
            'email' => $client->email,
            'phone' => $client->phone,
            'address' => $client->address,
            'tax_id' => $client->tax_id,
        ];
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();
        
        if ($this->isEditing) {
            $this->editingClient->update($this->form);
        } else {
            Client::create($this->form);
        }
        
        $this->showModal = false;
        $this->resetForm();
        
        session()->flash('message', $this->isEditing ? 'Client updated successfully.' : 'Client created successfully.');
    }

    public function confirmDelete($clientId = null)
    {
        if ($clientId) {
            $this->selectedClients = [$clientId];
        }
        $this->showDeleteModal = true;
    }

    public function deleteSelected()
    {
        DB::transaction(function () {
            // Delete clients and their related records
            foreach ($this->selectedClients as $clientId) {
                $client = Client::find($clientId);
                if ($client) {
                    $client->delete();
                }
            }
        });
        
        $this->selectedClients = [];
        $this->selectAll = false;
        $this->showDeleteModal = false;
        
        session()->flash('message', 'Selected clients deleted successfully.');
    }

    public function manageRelationships($clientId)
    {
        $this->ownershipClient = Client::findOrFail($clientId);
        $this->loadCurrentRelationships();
        $this->selectedOwner = '';
        $this->selectedCompany = '';
        $this->ownershipModal = true;
    }

    public function loadCurrentRelationships()
    {
        if ($this->ownershipClient->type === 'individual') {
            $this->selectedCompany = $this->ownershipClient->ownedCompanies()
                ->pluck('company_id')
                ->first();
        } else {
            $this->selectedOwner = $this->ownershipClient->owners()
                ->pluck('owner_id')
                ->first();
        }
    }

    public function saveRelationship()
    {
        if ($this->ownershipClient->type === 'individual' && $this->selectedCompany) {
            // Remove existing relationships
            ClientRelationship::where('owner_id', $this->ownershipClient->id)->delete();
            
            // Create new relationship
            ClientRelationship::create([
                'owner_id' => $this->ownershipClient->id,
                'company_id' => $this->selectedCompany,
            ]);
        } elseif ($this->ownershipClient->type === 'company' && $this->selectedOwner) {
            // Remove existing relationships
            ClientRelationship::where('company_id', $this->ownershipClient->id)->delete();
            
            // Create new relationship
            ClientRelationship::create([
                'owner_id' => $this->selectedOwner,
                'company_id' => $this->ownershipClient->id,
            ]);
        }
        
        $this->ownershipModal = false;
        
        session()->flash('message', 'Relationship updated successfully.');
    }

    public function getIndividualsProperty()
    {
        if (!$this->ownershipClient) {
            return collect([]);
        }
        
        // Get individuals that don't already own this company
        if ($this->ownershipClient->type === 'company') {
            $ownerIds = $this->ownershipClient->owners()->pluck('owner_id');
            return Client::individuals()
                ->whereNotIn('id', [$this->ownershipClient->id]) // Exclude self
                ->whereNotIn('id', $ownerIds) // Exclude already owners
                ->pluck('name', 'id')
                ->toArray();
        }
        
        return collect([]);
    }

    public function getCompaniesProperty()
    {
        if (!$this->ownershipClient) {
            return collect([]);
        }
        
        // Get companies that are not already owned by this individual
        if ($this->ownershipClient->type === 'individual') {
            $ownedCompanyIds = $this->ownershipClient->ownedCompanies()->pluck('company_id');
            return Client::companies()
                ->whereNotIn('id', [$this->ownershipClient->id]) // Exclude self
                ->whereNotIn('id', $ownedCompanyIds) // Exclude already owned
                ->pluck('name', 'id')
                ->toArray();
        }
        
        return collect([]);
    }

    public function resetForm()
    {
        $this->form = [
            'name' => '',
            'type' => 'individual',
            'email' => '',
            'phone' => '',
            'address' => '',
            'tax_id' => '',
        ];
        $this->editingClient = null;
    }

    public function render()
    {
        return view('livewire.client-management');
    }
}