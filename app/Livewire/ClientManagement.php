<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\ClientRelationship;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;

class ClientManagement extends Component
{
    use WithPagination;

    // Search & filter properties
    public $search = '';
    public $type = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    
    // Form properties
    public $showForm = false;
    public $isEdit = false;
    public $clientId = null;
    public $name = '';
    public $email = '';
    public $phone = '';
    public $address = '';
    public $taxId = '';
    public $clientType = 'individual';
    
    // Company relationship properties
    public $showCompanyModal = false;
    public $companySearch = '';
    public $selectedCompanies = [];
    public $availableCompanies = [];
    
    // Individual owners relationship properties
    public $showOwnerModal = false;
    public $ownerSearch = '';
    public $selectedOwners = [];
    public $availableOwners = [];
    
    // Client detail properties
    public $showDetail = false;
    public $viewingClient = null;
    
    // Confirmation dialog
    public $showDeleteConfirmation = false;
    public $clientToDelete = null;

    // Listeners
    protected $listeners = ['refreshClients' => '$refresh'];

    // Rules for validation
    protected function rules()
    {
        return [
            'name' => 'required|min:3',
            'email' => 'nullable|email',
            'phone' => 'nullable',
            'address' => 'nullable',
            'taxId' => 'nullable',
            'clientType' => 'required|in:individual,company',
        ];
    }

    // Reset pagination when filters change
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingType()
    {
        $this->resetPage();
    }

    // Sort data
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    // Open/close form modal
    public function openForm()
    {
        $this->reset([
            'name', 'email', 'phone', 'address', 'taxId', 'clientType',
            'clientId', 'isEdit', 'selectedCompanies', 'selectedOwners'
        ]);
        
        $this->showForm = true;
        $this->isEdit = false;
    }

    public function closeForm()
    {
        $this->showForm = false;
    }
    
    // Save client (create or update)
    public function saveClient()
    {
        $this->validate();
        
        if ($this->isEdit) {
            $client = Client::find($this->clientId);
            if (!$client) {
                session()->flash('error', 'Client not found');
                return;
            }
        } else {
            $client = new Client();
        }
        
        // Set client attributes
        $client->name = $this->name;
        $client->email = $this->email;
        $client->phone = $this->phone;
        $client->address = $this->address;
        $client->tax_id = $this->taxId;
        $client->type = $this->clientType;
        
        $client->save();
        
        // Update relationships if needed
        if ($this->clientType === 'individual') {
            // Update the company relationships
            $this->updateCompanyRelationships($client);
        } else {
            // Update the owner relationships
            $this->updateOwnerRelationships($client);
        }
        
        // Success message and reset form
        session()->flash('message', 'Client successfully saved.');
        $this->closeForm();
    }
    
    // Edit client
    public function editClient($id)
    {
        $client = Client::findOrFail($id);
        
        $this->clientId = $client->id;
        $this->name = $client->name;
        $this->email = $client->email;
        $this->phone = $client->phone;
        $this->address = $client->address;
        $this->taxId = $client->tax_id;
        $this->clientType = $client->type;
        $this->isEdit = true;
        
        // Load relationships
        if ($client->type === 'individual') {
            $this->selectedCompanies = $client->ownedCompanies()->pluck('id')->toArray();
        } else {
            $this->selectedOwners = $client->owners()->pluck('id')->toArray();
        }
        
        $this->showForm = true;
    }
    
    // View client details
    public function viewClientDetails($id)
    {
        $this->viewingClient = Client::with([
            'ownedCompanies', 
            'owners', 
            'serviceClients.service',
            'invoices'
        ])->findOrFail($id);
        
        $this->showDetail = true;
    }
    
    // Close detail view
    public function closeDetailView()
    {
        $this->showDetail = false;
        $this->viewingClient = null;
    }
    
    // Delete confirmation
    public function confirmDelete($id)
    {
        $this->clientToDelete = $id;
        $this->showDeleteConfirmation = true;
    }
    
    // Cancel delete
    public function cancelDelete()
    {
        $this->showDeleteConfirmation = false;
        $this->clientToDelete = null;
    }
    
    // Delete client
    public function deleteClient()
    {
        $client = Client::find($this->clientToDelete);
        
        if ($client) {
            // First remove any relationships
            ClientRelationship::where('owner_id', $client->id)
                ->orWhere('company_id', $client->id)
                ->delete();
                
            // Then delete the client
            $client->delete();
            
            session()->flash('message', 'Client successfully deleted.');
        }
        
        $this->showDeleteConfirmation = false;
        $this->clientToDelete = null;
    }
    
    // Company selection modal
    public function openCompanyModal()
    {
        $this->loadAvailableCompanies();
        $this->showCompanyModal = true;
    }
    
    public function closeCompanyModal()
    {
        $this->showCompanyModal = false;
    }
    
    protected function loadAvailableCompanies()
    {
        $this->availableCompanies = Client::where('type', 'company')
            ->when($this->companySearch, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->get()
            ->toArray();
    }
    
    public function updatedCompanySearch()
    {
        $this->loadAvailableCompanies();
    }
    
    public function toggleCompany($companyId)
    {
        if (in_array($companyId, $this->selectedCompanies)) {
            $this->selectedCompanies = array_diff($this->selectedCompanies, [$companyId]);
        } else {
            $this->selectedCompanies[] = $companyId;
        }
    }
    
    // Owner selection modal
    public function openOwnerModal()
    {
        $this->loadAvailableOwners();
        $this->showOwnerModal = true;
    }
    
    public function closeOwnerModal()
    {
        $this->showOwnerModal = false;
    }
    
    protected function loadAvailableOwners()
    {
        $this->availableOwners = Client::where('type', 'individual')
            ->when($this->ownerSearch, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->get()
            ->toArray();
    }
    
    public function updatedOwnerSearch()
    {
        $this->loadAvailableOwners();
    }
    
    public function toggleOwner($ownerId)
    {
        if (in_array($ownerId, $this->selectedOwners)) {
            $this->selectedOwners = array_diff($this->selectedOwners, [$ownerId]);
        } else {
            $this->selectedOwners[] = $ownerId;
        }
    }
    
    // Update relationship methods
    protected function updateCompanyRelationships(Client $client)
    {
        // Delete existing relationships
        ClientRelationship::where('owner_id', $client->id)->delete();
        
        // Create new relationships
        foreach ($this->selectedCompanies as $companyId) {
            ClientRelationship::create([
                'owner_id' => $client->id,
                'company_id' => $companyId
            ]);
        }
    }
    
    protected function updateOwnerRelationships(Client $client)
    {
        // Delete existing relationships
        ClientRelationship::where('company_id', $client->id)->delete();
        
        // Create new relationships
        foreach ($this->selectedOwners as $ownerId) {
            ClientRelationship::create([
                'owner_id' => $ownerId,
                'company_id' => $client->id
            ]);
        }
    }
    
    // Main render method
    public function render()
    {
        $clients = Client::when($this->search, function (Builder $query, $search) {
                return $query->where(function (Builder $subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('tax_id', 'like', "%{$search}%");
                });
            })
            ->when($this->type, function (Builder $query, $type) {
                return $query->where('type', $type);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);
            
        return view('livewire.client-management', [
            'clients' => $clients,
        ]);
    }
}