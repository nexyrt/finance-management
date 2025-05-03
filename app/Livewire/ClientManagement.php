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

    // Pagination properties
    public $perPage = 10;
    public $gotoPage = 1;

    // Form properties
    public $name = '';
    public $email = '';
    public $phone = '';
    public $address = '';
    public $taxId = '';
    public $clientType = 'individual';
    public $clientId = null;

    // Selection properties
    public $selectAll = false;
    public $selectedClients = [];

    // Relationship properties
    public $companySearch = '';
    public $ownerSearch = '';
    public $selectedCompanies = [];
    public $selectedOwners = [];
    public $availableCompanies = [];
    public $availableOwners = [];
    public $displayedCompanies = [];
    public $displayedOwners = [];

    // Client detail properties
    public $viewingClient = null;
    public $clientToDelete = null;
    public $hasDependencies = false;
    public $clientDependencies = [];

    // Event listeners
    protected $listeners = [
        'selectAllClients',
        'selectClient',
        'clientPageChanged' => 'refreshClientModals'
    ];

    // Validation rules
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
    public function updatingSearch() { $this->resetPage(); }
    public function updatingType() { $this->resetPage(); }
    public function updatingPerPage() { $this->resetPage(); }
    public function updatingPage($page) { $this->gotoPage = $page; }

    // Methods for client selection
    public function selectClient($clientId, $isSelected)
    {
        $clientId = (int) $clientId;
        
        if ($isSelected) {
            if (!in_array($clientId, $this->selectedClients)) {
                $this->selectedClients[] = $clientId;
            }
        } else {
            $this->selectedClients = array_values(array_diff($this->selectedClients, [$clientId]));
            $this->selectAll = false;
        }
    }

    public function selectAllClients($isSelected)
    {
        $this->selectAll = $isSelected;

        if ($isSelected) {
            $this->selectedClients = $this->getFilteredClientIds();
        } else {
            $this->selectedClients = [];
        }
    }

    private function getFilteredClientIds()
    {
        return Client::query()
            ->when($this->search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('tax_id', 'like', "%{$search}%");
                });
            })
            ->when($this->type, fn($query) => $query->where('type', $this->type))
            ->pluck('id')
            ->toArray();
    }

    // Bulk delete clients
    public function bulkDeleteClients()
    {
        if (empty($this->selectedClients)) {
            session()->flash('error', 'No clients selected for deletion.');
            return;
        }

        $totalCount = count($this->selectedClients);
        $successCount = 0;
        $skippedCount = 0;
        $skippedNames = [];
        $skippedDetails = [];

        foreach ($this->selectedClients as $clientId) {
            $client = Client::find($clientId);

            if (!$client) {
                continue;
            }

            // Check for dependencies
            $serviceClientsCount = $client->serviceClients()->count();
            $invoicesCount = $client->invoices()->count();
            $hasDependencies = ($serviceClientsCount > 0 || $invoicesCount > 0);

            if ($hasDependencies) {
                $skippedCount++;
                $skippedNames[] = $client->name;

                $skippedDetails[] = [
                    'name' => $client->name,
                    'serviceClients' => $serviceClientsCount,
                    'invoices' => $invoicesCount
                ];
                continue;
            }

            try {
                DB::transaction(function () use ($client) {
                    ClientRelationship::where('owner_id', $client->id)
                        ->orWhere('company_id', $client->id)
                        ->delete();
                    $client->delete();
                });
                $successCount++;
            } catch (\Exception $e) {
                \Log::error('Error deleting client: ' . $e->getMessage());
            }
        }

        // Store detailed information for the view
        if (!empty($skippedDetails)) {
            session()->flash('skippedDetails', $skippedDetails);
        }

        // Set appropriate message
        if ($successCount > 0) {
            if ($skippedCount > 0) {
                session()->flash('message', "{$successCount} clients were deleted successfully. {$skippedCount} clients were skipped due to dependencies.");
            } else {
                session()->flash('message', "{$successCount} clients were deleted successfully.");
            }
        } else if ($skippedCount > 0) {
            session()->flash('error', "No clients were deleted. {$skippedCount} clients were skipped due to dependencies.");
        } else {
            session()->flash('error', "Failed to delete any clients. Please try again.");
        }

        // Reset selection state
        $this->selectedClients = [];
        $this->selectAll = false;
    }

    // Client type and relationship display handling
    public function updatedClientType()
    {
        if ($this->clientType === 'individual') {
            $this->displayedOwners = [];
            $this->updateDisplayedCompanies();
        } else {
            $this->displayedCompanies = [];
            $this->updateDisplayedOwners();
        }
    }

    protected function updateDisplayedCompanies()
    {
        $this->displayedCompanies = [];
        $companyIds = array_filter($this->selectedCompanies, 'is_numeric');
        
        if (!empty($companyIds)) {
            $companies = Client::whereIn('id', $companyIds)->get(['id', 'name']);
            foreach ($companies as $company) {
                $this->displayedCompanies[] = [
                    'id' => $company->id,
                    'name' => $company->name
                ];
            }
        }
    }

    protected function updateDisplayedOwners()
    {
        $this->displayedOwners = [];
        $ownerIds = array_filter($this->selectedOwners, 'is_numeric');
        
        if (!empty($ownerIds)) {
            $owners = Client::whereIn('id', $ownerIds)->get(['id', 'name']);
            foreach ($owners as $owner) {
                $this->displayedOwners[] = [
                    'id' => $owner->id,
                    'name' => $owner->name
                ];
            }
        }
    }

    // Form methods - Modal triggers
    public function prepareCreate()
    {
        // Reset all form fields
        $this->reset([
            'name', 'email', 'phone', 'address', 'taxId', 'clientType',
            'clientId', 'selectedCompanies', 'selectedOwners',
            'displayedCompanies', 'displayedOwners'
        ]);

        // Set default values
        $this->clientType = 'individual';
        
        // Preload available companies and owners
        $this->loadAvailableCompanies();
        $this->loadAvailableOwners();
    }

    public function saveClient()
    {
        $this->validate();

        try {
            DB::transaction(function () {
                $client = $this->clientId ? Client::findOrFail($this->clientId) : new Client();
                
                $client->name = $this->name;
                $client->email = $this->email;
                $client->phone = $this->phone;
                $client->address = $this->address;
                $client->tax_id = $this->taxId;
                $client->type = $this->clientType;
                $client->save();

                // Update relationships
                if ($this->clientType === 'individual') {
                    $this->updateCompanyRelationships($client);
                } else {
                    $this->updateOwnerRelationships($client);
                }
            });

            session()->flash('message', 'Client successfully saved.');
            
            // Reset form state
            $this->reset([
                'name', 'email', 'phone', 'address', 'taxId', 'clientId',
                'selectedCompanies', 'selectedOwners', 'displayedCompanies', 'displayedOwners'
            ]);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error saving client: ' . $e->getMessage());
        }
    }

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

        // Reset and load relationships
        $this->selectedCompanies = [];
        $this->selectedOwners = [];
        $this->displayedCompanies = [];
        $this->displayedOwners = [];

        if ($client->type === 'individual') {
            $this->selectedCompanies = $client->ownedCompanies()->pluck('clients.id')->toArray();
            $this->updateDisplayedCompanies();
        } else {
            $this->selectedOwners = $client->owners()->pluck('clients.id')->toArray();
            $this->updateDisplayedOwners();
        }

        $this->loadAvailableCompanies();
        $this->loadAvailableOwners();
    }

    public function clearEditForm()
    {
        $this->reset([
            'clientId', 'name', 'email', 'phone', 'address', 'taxId', 
            'selectedCompanies', 'selectedOwners', 'displayedCompanies', 'displayedOwners'
        ]);
    }

    // Client details, delete, and modals
    public function loadClientDetails($id)
    {
        $this->viewingClient = Client::with([
            'ownedCompanies',
            'owners',
            'serviceClients.service',
            'serviceClients.invoiceItems',
            'invoices'
        ])->find($id);
    }

    public function clearViewingClient()
    {
        $this->viewingClient = null;
    }

    public function confirmDelete($id)
    {
        $client = Client::find($id);
        if (!$client) {
            session()->flash('error', 'Client not found.');
            return;
        }

        $this->clientToDelete = $id;
        $serviceClientsCount = $client->serviceClients()->count();
        $invoicesCount = $client->invoices()->count();

        $this->clientDependencies = [
            'serviceClients' => $serviceClientsCount,
            'invoices' => $invoicesCount
        ];

        $this->hasDependencies = ($serviceClientsCount > 0 || $invoicesCount > 0);
    }

    public function clearDeleteConfirmation()
    {
        $this->clientToDelete = null;
        $this->hasDependencies = false;
        $this->clientDependencies = [];
    }

    public function deleteClient()
    {
        $client = Client::find($this->clientToDelete);
        if (!$client) {
            session()->flash('error', 'Client not found.');
            $this->clearDeleteConfirmation();
            return;
        }

        if ($client->serviceClients()->exists() || $client->invoices()->exists()) {
            session()->flash('error', 'Cannot delete client. Please remove associated services and invoices first.');
            $this->clearDeleteConfirmation();
            return;
        }

        try {
            DB::transaction(function () use ($client) {
                ClientRelationship::where('owner_id', $client->id)
                    ->orWhere('company_id', $client->id)
                    ->delete();
                    
                $client->delete();
            });

            session()->flash('message', 'Client successfully deleted.');
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred: ' . $e->getMessage());
        }

        $this->clearDeleteConfirmation();
    }

    public function refreshClientModals()
    {
        if ($this->viewingClient) {
            $this->loadClientDetails($this->viewingClient->id);
        }
        $this->gotoPage = $this->page; // Sync the page selector with the current page
    }

    // Relationship selection methods
    protected function loadAvailableCompanies()
    {
        $query = Client::where('type', 'company');
        
        if ($this->companySearch) {
            $query->where('name', 'like', "%{$this->companySearch}%");
        }
        
        $this->availableCompanies = $query->orderBy('name')->get(['id', 'name'])->toArray();
    }

    protected function loadAvailableOwners()
    {
        $query = Client::where('type', 'individual');
        
        if ($this->ownerSearch) {
            $query->where('name', 'like', "%{$this->ownerSearch}%");
        }
        
        $this->availableOwners = $query->orderBy('name')->get(['id', 'name'])->toArray();
    }

    public function updatedCompanySearch() { $this->loadAvailableCompanies(); }
    public function updatedOwnerSearch() { $this->loadAvailableOwners(); }

    public function toggleCompany($companyId)
    {
        $companyId = (int) $companyId;
        
        if (in_array($companyId, $this->selectedCompanies)) {
            $this->selectedCompanies = array_values(array_diff($this->selectedCompanies, [$companyId]));
        } else {
            $this->selectedCompanies[] = $companyId;
        }

        $this->updateDisplayedCompanies();
    }

    public function toggleOwner($ownerId)
    {
        $ownerId = (int) $ownerId;
        
        if (in_array($ownerId, $this->selectedOwners)) {
            $this->selectedOwners = array_values(array_diff($this->selectedOwners, [$ownerId]));
        } else {
            $this->selectedOwners[] = $ownerId;
        }

        $this->updateDisplayedOwners();
    }

    protected function updateCompanyRelationships(Client $client)
    {
        ClientRelationship::where('owner_id', $client->id)->delete();

        $validCompanyIds = array_filter($this->selectedCompanies, 'is_numeric');
        foreach ($validCompanyIds as $companyId) {
            ClientRelationship::create([
                'owner_id' => $client->id,
                'company_id' => $companyId
            ]);
        }
    }

    protected function updateOwnerRelationships(Client $client)
    {
        ClientRelationship::where('company_id', $client->id)->delete();

        $validOwnerIds = array_filter($this->selectedOwners, 'is_numeric');
        foreach ($validOwnerIds as $ownerId) {
            ClientRelationship::create([
                'owner_id' => $ownerId,
                'company_id' => $client->id
            ]);
        }
    }

    // Selector modal helpers
    public function openCompanySelector() { $this->loadAvailableCompanies(); }
    public function openOwnerSelector() { $this->loadAvailableOwners(); }
    public function closeCompanySelector() { $this->updateDisplayedCompanies(); }
    public function closeOwnerSelector() { $this->updateDisplayedOwners(); }

    // Sorting and pagination
    public function sortBy($field)
    {
        $this->sortDirection = ($this->sortField === $field) 
            ? ($this->sortDirection === 'asc' ? 'desc' : 'asc')
            : 'asc';
        $this->sortField = $field;
    }

    public function goToPage()
    {
        // Sanitize input
        $this->gotoPage = max(1, (int) $this->gotoPage);
        
        // Calculate last page
        $query = $this->getBaseQuery();
        $count = $query->count();
        $lastPage = max(1, ceil($count / $this->perPage));
        
        // Ensure page is within bounds
        $this->gotoPage = min($this->gotoPage, $lastPage);
        
        // Navigate to page
        $this->setPage($this->gotoPage);
    }

    private function getBaseQuery()
    {
        return Client::query()
            ->when($this->search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('tax_id', 'like', "%{$search}%");
                });
            })
            ->when($this->type, function ($query, $type) {
                return $query->where('type', $type);
            });
    }

    // Main render method
    public function render()
    {
        $query = $this->getBaseQuery()->orderBy($this->sortField, $this->sortDirection);
        
        if ($this->perPage === 'All') {
            $clients = $query->get();
        } else {
            $clients = $query->paginate($this->perPage);
        }

        // Get all client data for detail modals
        $allClients = Client::all()->keyBy('id');

        return view('livewire.client-management', [
            'clients' => $clients,
            'allClients' => $allClients,
        ]);
    }
}