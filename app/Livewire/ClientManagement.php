<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\ClientRelationship;
use Illuminate\Support\Facades\DB;
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
    public $isEdit = false;
    public $clientId = null;
    public $name = '';
    public $email = '';
    public $phone = '';
    public $address = '';
    public $taxId = '';
    public $clientType = 'individual';

    // Company relationship properties
    public $companySearch = '';
    public $selectedCompanies = [];
    public $availableCompanies = [];
    public $displayedCompanies = []; // New property to track displayed companies

    // Individual owners relationship properties
    public $ownerSearch = '';
    public $selectedOwners = [];
    public $availableOwners = [];
    public $displayedOwners = []; // New property to track displayed owners

    // Client detail properties
    public $viewingClient = null;

    // Dependency tracking for delete confirmation
    public $hasDependencies = false;
    public $clientDependencies = [];
    public $clientToDelete = null;

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

    // When client type changes, update the display properties but keep selections
    public function updatedClientType()
    {
        if ($this->clientType === 'individual') {
            // Store the current owners before hiding them
            $this->displayedOwners = [];
            
            // Make sure to display companies
            $this->updateDisplayedCompanies();
        } else {
            // Store the current companies before hiding them
            $this->displayedCompanies = [];
            
            // Make sure to display owners
            $this->updateDisplayedOwners();
        }
    }
    
    // Update displayed companies based on selected companies
    protected function updateDisplayedCompanies()
    {
        $this->displayedCompanies = [];
        foreach ($this->selectedCompanies as $companyId) {
            $company = Client::find($companyId);
            if ($company) {
                $this->displayedCompanies[] = [
                    'id' => $company->id,
                    'name' => $company->name
                ];
            }
        }
    }
    
    // Update displayed owners based on selected owners
    protected function updateDisplayedOwners()
    {
        $this->displayedOwners = [];
        foreach ($this->selectedOwners as $ownerId) {
            $owner = Client::find($ownerId);
            if ($owner) {
                $this->displayedOwners[] = [
                    'id' => $owner->id,
                    'name' => $owner->name
                ];
            }
        }
    }

    // Prepare creation form
    public function prepareCreate()
    {
        $this->reset([
            'name',
            'email',
            'phone',
            'address',
            'taxId',
            'clientType',
            'clientId',
            'isEdit',
            'selectedCompanies',
            'selectedOwners',
            'displayedCompanies',
            'displayedOwners'
        ]);

        $this->clientType = 'individual';
        $this->isEdit = false;

        // Load available companies and owners for selection
        $this->loadAvailableCompanies();
        $this->loadAvailableOwners();
    }

    // Save client (create or update)
    public function saveClient()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            if ($this->isEdit) {
                $client = Client::find($this->clientId);
                if (!$client) {
                    session()->flash('error', 'Client not found');
                    DB::rollBack();
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

            DB::commit();

            // Success message
            session()->flash('message', 'Client successfully saved.');

            // Reset form properties
            $this->reset([
                'name', 'email', 'phone', 'address', 'taxId', 'clientId', 'isEdit',
                'selectedCompanies', 'selectedOwners', 'displayedCompanies', 'displayedOwners'
            ]);
            
            // Close modal by dispatching browser event
            $this->dispatch('closeModal');
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error saving client: ' . $e->getMessage());
        }
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

        // Reset relationship arrays
        $this->selectedCompanies = [];
        $this->selectedOwners = [];
        $this->displayedCompanies = [];
        $this->displayedOwners = [];

        // Load relationships - with explicit columns to avoid ambiguity
        if ($client->type === 'individual') {
            $this->selectedCompanies = $client->ownedCompanies()
                ->select('clients.id')
                ->pluck('clients.id')
                ->toArray();
                
            $this->updateDisplayedCompanies();
        } else {
            $this->selectedOwners = $client->owners()
                ->select('clients.id')
                ->pluck('clients.id')
                ->toArray();
                
            $this->updateDisplayedOwners();
        }

        // Load available options for selection
        $this->loadAvailableCompanies();
        $this->loadAvailableOwners();
    }

    // Load client details
    public function loadClientDetails($id)
    {
        $this->viewingClient = Client::with([
            'ownedCompanies',
            'owners',
            'serviceClients.service',
            'serviceClients.invoiceItems',
            'invoices'
        ])->findOrFail($id);
    }

    // Confirm delete
    public function confirmDelete($id)
    {
        $this->clientToDelete = $id;
        $client = Client::find($id);

        if (!$client) {
            session()->flash('error', 'Client not found.');
            return;
        }

        // Check for dependencies
        $serviceClientsCount = $client->serviceClients()->count();
        $invoicesCount = $client->invoices()->count();

        $this->clientDependencies = [
            'serviceClients' => $serviceClientsCount,
            'invoices' => $invoicesCount
        ];

        $this->hasDependencies = ($serviceClientsCount > 0 || $invoicesCount > 0);
    }

    // Delete client
    public function deleteClient()
    {
        $client = Client::find($this->clientToDelete);

        if (!$client) {
            session()->flash('error', 'Client not found.');
            $this->resetDeleteProperties();
            return;
        }

        // Check for service_clients records
        $hasServiceClients = $client->serviceClients()->exists();

        // Check for invoices
        $hasInvoices = $client->invoices()->exists();

        if ($hasServiceClients || $hasInvoices) {
            session()->flash('error', 'Cannot delete client. Please remove associated services and invoices first.');
            $this->resetDeleteProperties();
            return;
        }

        try {
            // Begin a database transaction
            DB::beginTransaction();

            // First remove any relationships
            ClientRelationship::where('owner_id', $client->id)
                ->orWhere('company_id', $client->id)
                ->delete();

            // Then delete the client
            $client->delete();

            // Commit the transaction
            DB::commit();

            session()->flash('message', 'Client successfully deleted.');
            
            // Close modal
            $this->dispatch('closeModal');
            
        } catch (\Exception $e) {
            // Rollback the transaction if any errors occur
            DB::rollBack();
            session()->flash('error', 'An error occurred: ' . $e->getMessage());
        }

        $this->resetDeleteProperties();
    }
    
    // Reset delete-related properties
    protected function resetDeleteProperties()
    {
        $this->clientToDelete = null;
        $this->hasDependencies = false;
        $this->clientDependencies = [];
    }

    // Company selection
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
        
        // Update displayed companies to match selection
        $this->updateDisplayedCompanies();
    }

    // Owner selection
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
        
        // Update displayed owners to match selection
        $this->updateDisplayedOwners();
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
    
    // Handle cleanup when selector modals are closed
    public function openCompanySelector()
    {
        $this->loadAvailableCompanies();
    }
    
    public function openOwnerSelector()
    {
        $this->loadAvailableOwners();
    }
    
    public function closeCompanySelector()
    {
        $this->updateDisplayedCompanies();
    }
    
    public function closeOwnerSelector()
    {
        $this->updateDisplayedOwners();
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