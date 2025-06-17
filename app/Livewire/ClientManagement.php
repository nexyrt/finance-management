<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Client;
use App\Models\ClientRelationship;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;

class ClientManagement extends Component
{
    use WithPagination, WithFileUploads;

    // Client Form Properties
    public $name = '';
    public $type = 'individual';
    public $email = '';
    public $NPWP = '';
    public $KPP = '';
    public $logo = null;
    public $status = 'Active';
    public $EFIN = '';
    public $account_representative = '';
    public $ar_phone_number = '';
    public $person_in_charge = '';
    public $address = '';

    // File upload
    public $uploadedLogo = null;

    // Relationship Management
    public $selectedOwners = [];
    public $selectedCompanies = [];
    public $availableOwners = [];
    public $availableCompanies = [];

    // Modal State
    public $showAddClientModal = false;
    public $showEditClientModal = false;
    public $showDeleteModal = false;
    public $showDetailModal = false;
    public $showRelationshipModal = false;
    public $showBulkActionModal = false;

    // Search and Filter
    public $search = '';
    public $filterType = '';
    public $filterStatus = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    // Bulk Operations
    public $selectedClients = [];
    public $bulkAction = '';

    // Edit State
    public $editingClient = null;
    public $clientToDelete = null;
    public $clientDetail = null;

    protected function rules()
    {
        $clientId = $this->editingClient ? $this->editingClient->id : null;
        
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|in:individual,company',
            'email' => 'nullable|email|unique:clients,email,' . $clientId,
            'NPWP' => 'nullable|string|max:20',
            'KPP' => 'nullable|string|max:20',
            'status' => 'required|in:Active,Inactive',
            'EFIN' => 'nullable|string|max:20',
            'account_representative' => 'nullable|string|max:255',
            'ar_phone_number' => 'nullable|string|max:20',
            'person_in_charge' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'uploadedLogo' => 'nullable|image|max:2048', // 2MB max
        ];
    }

    protected $messages = [
        'name.required' => 'Nama klien wajib diisi.',
        'type.required' => 'Tipe klien wajib dipilih.',
        'email.email' => 'Format email tidak valid.',
        'email.unique' => 'Email sudah digunakan klien lain.',
        'uploadedLogo.image' => 'File harus berupa gambar.',
        'uploadedLogo.max' => 'Ukuran file maksimal 2MB.',
    ];

    public function mount()
    {
        $this->loadRelationshipData();
    }

    // Computed Properties
    #[Computed]
    public function totalClients()
    {
        return Client::count();
    }

    #[Computed]
    public function totalIndividuals()
    {
        return Client::where('type', 'individual')->count();
    }

    #[Computed]
    public function totalCompanies()
    {
        return Client::where('type', 'company')->count();
    }

    #[Computed]
    public function activeClients()
    {
        return Client::where('status', 'Active')->count();
    }

    #[Computed]
    public function recentClients()
    {
        return Client::orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($client) {
                return [
                    'id' => $client->id,
                    'name' => $client->name,
                    'type' => $client->type,
                    'email' => $client->email,
                    'status' => $client->status,
                    'created_at' => $client->created_at,
                    'formatted_date' => Carbon::parse($client->created_at)->diffForHumans(),
                ];
            });
    }

    #[Computed]
    public function clients()
    {
        $query = Client::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('NPWP', 'like', '%' . $this->search . '%')
                    ->orWhere('account_representative', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterType) {
            $query->where('type', $this->filterType);
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        return $query->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(12);
    }

    #[Computed]
    public function clientStats()
    {
        $totalInvoices = DB::table('invoices')
            ->join('clients', 'invoices.billed_to_id', '=', 'clients.id')
            ->count();

        $totalRevenue = DB::table('invoices')
            ->join('clients', 'invoices.billed_to_id', '=', 'clients.id')
            ->where('invoices.status', 'paid')
            ->sum('invoices.total_amount');

        return [
            'total_invoices' => $totalInvoices,
            'total_revenue' => $totalRevenue,
            'average_per_client' => $this->totalClients > 0 ? $totalRevenue / $this->totalClients : 0,
        ];
    }

    // Modal Management
    public function openAddClientModal()
    {
        $this->resetClientForm();
        $this->loadRelationshipData();
        $this->showAddClientModal = true;
    }

    public function openEditClientModal($clientId)
    {
        $this->editingClient = Client::with(['owners', 'ownedCompanies'])->findOrFail($clientId);
        $this->fillFormFromClient($this->editingClient);
        $this->loadRelationshipData();
        $this->showEditClientModal = true;
    }

    public function openDetailModal($clientId)
    {
        $this->clientDetail = Client::with(['owners', 'ownedCompanies', 'invoices', 'invoiceItems'])
            ->findOrFail($clientId);
        $this->showDetailModal = true;
    }

    public function openRelationshipModal($clientId)
    {
        $this->editingClient = Client::with(['owners', 'ownedCompanies'])->findOrFail($clientId);
        $this->loadRelationshipData();
        $this->loadCurrentRelationships();
        $this->showRelationshipModal = true;
    }

    public function confirmDeleteClient($clientId)
    {
        $this->clientToDelete = Client::findOrFail($clientId);
        $this->showDeleteModal = true;
    }

    // Client CRUD Operations
    public function saveClient()
    {
        $this->validate();

        try {
            DB::transaction(function () {
                // Handle logo upload
                $logoPath = null;
                if ($this->uploadedLogo) {
                    $logoPath = $this->uploadedLogo->store('client-logos', 'public');
                }

                $client = Client::create([
                    'name' => $this->name,
                    'type' => $this->type,
                    'email' => $this->email,
                    'NPWP' => $this->NPWP,
                    'KPP' => $this->KPP,
                    'logo' => $logoPath,
                    'status' => $this->status,
                    'EFIN' => $this->EFIN,
                    'account_representative' => $this->account_representative,
                    'ar_phone_number' => $this->ar_phone_number,
                    'person_in_charge' => $this->person_in_charge,
                    'address' => $this->address,
                ]);

                // Handle relationships
                $this->saveRelationships($client);
            });

            $this->dispatch('notify', type: 'success', message: 'Klien berhasil ditambahkan!');
            $this->showAddClientModal = false;
            $this->resetClientForm();
            
            // Clear computed property cache
            unset($this->totalClients, $this->totalIndividuals, $this->totalCompanies, $this->clients);
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Gagal menambahkan klien: ' . $e->getMessage());
        }
    }

    public function updateClient()
    {
        $this->validate();

        try {
            DB::transaction(function () {
                // Handle logo upload
                $logoPath = $this->editingClient->logo;
                if ($this->uploadedLogo) {
                    // Delete old logo
                    if ($logoPath) {
                        Storage::disk('public')->delete($logoPath);
                    }
                    $logoPath = $this->uploadedLogo->store('client-logos', 'public');
                }

                $this->editingClient->update([
                    'name' => $this->name,
                    'type' => $this->type,
                    'email' => $this->email,
                    'NPWP' => $this->NPWP,
                    'KPP' => $this->KPP,
                    'logo' => $logoPath,
                    'status' => $this->status,
                    'EFIN' => $this->EFIN,
                    'account_representative' => $this->account_representative,
                    'ar_phone_number' => $this->ar_phone_number,
                    'person_in_charge' => $this->person_in_charge,
                    'address' => $this->address,
                ]);

                // Handle relationships
                $this->saveRelationships($this->editingClient);
            });

            $this->dispatch('notify', type: 'success', message: 'Klien berhasil diperbarui!');
            $this->showEditClientModal = false;
            $this->resetClientForm();
            
            // Clear computed property cache
            unset($this->clients, $this->recentClients);
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Gagal memperbarui klien: ' . $e->getMessage());
        }
    }

    public function deleteClient()
    {
        try {
            DB::transaction(function () {
                // Delete logo file
                if ($this->clientToDelete->logo) {
                    Storage::disk('public')->delete($this->clientToDelete->logo);
                }

                // Use the model's delete method which handles relationships
                $this->clientToDelete->delete();
            });

            $this->dispatch('notify', type: 'success', message: 'Klien berhasil dihapus!');
            $this->showDeleteModal = false;
            $this->clientToDelete = null;
            
            // Clear computed property cache
            unset($this->totalClients, $this->totalIndividuals, $this->totalCompanies, $this->clients);
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Gagal menghapus klien: ' . $e->getMessage());
        }
    }

    // Relationship Management
    public function saveRelationships($client)
    {
        if ($client->type === 'individual' && !empty($this->selectedCompanies)) {
            // Individual owns companies
            $client->ownedCompanies()->sync($this->selectedCompanies);
        } elseif ($client->type === 'company' && !empty($this->selectedOwners)) {
            // Company is owned by individuals
            $client->owners()->sync($this->selectedOwners);
        }
    }

    public function updateRelationships()
    {
        try {
            DB::transaction(function () {
                $this->saveRelationships($this->editingClient);
            });

            $this->dispatch('notify', type: 'success', message: 'Hubungan klien berhasil diperbarui!');
            $this->showRelationshipModal = false;
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Gagal memperbarui hubungan: ' . $e->getMessage());
        }
    }

    // Bulk Operations
    public function openBulkActionModal()
    {
        if (empty($this->selectedClients)) {
            $this->dispatch('notify', type: 'warning', message: 'Pilih klien terlebih dahulu!');
            return;
        }
        $this->showBulkActionModal = true;
    }

    public function processBulkAction()
    {
        if (empty($this->selectedClients) || empty($this->bulkAction)) {
            $this->dispatch('notify', type: 'warning', message: 'Pilih klien dan aksi terlebih dahulu!');
            return;
        }

        try {
            DB::transaction(function () {
                switch ($this->bulkAction) {
                    case 'activate':
                        Client::whereIn('id', $this->selectedClients)->update(['status' => 'Active']);
                        $message = 'Klien berhasil diaktifkan!';
                        break;
                    case 'deactivate':
                        Client::whereIn('id', $this->selectedClients)->update(['status' => 'Inactive']);
                        $message = 'Klien berhasil dinonaktifkan!';
                        break;
                    case 'delete':
                        $clients = Client::whereIn('id', $this->selectedClients)->get();
                        foreach ($clients as $client) {
                            if ($client->logo) {
                                Storage::disk('public')->delete($client->logo);
                            }
                            $client->delete();
                        }
                        $message = 'Klien berhasil dihapus!';
                        break;
                    default:
                        throw new \Exception('Aksi tidak valid');
                }
            });

            $this->dispatch('notify', type: 'success', message: $message);
            $this->showBulkActionModal = false;
            $this->selectedClients = [];
            $this->bulkAction = '';
            
            // Clear computed property cache
            unset($this->clients, $this->totalClients, $this->activeClients);
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Gagal memproses aksi: ' . $e->getMessage());
        }
    }

    // Helper Methods
    private function resetClientForm()
    {
        $this->name = '';
        $this->type = 'individual';
        $this->email = '';
        $this->NPWP = '';
        $this->KPP = '';
        $this->logo = null;
        $this->uploadedLogo = null;
        $this->status = 'Active';
        $this->EFIN = '';
        $this->account_representative = '';
        $this->ar_phone_number = '';
        $this->person_in_charge = '';
        $this->address = '';
        $this->selectedOwners = [];
        $this->selectedCompanies = [];
        $this->editingClient = null;
        $this->resetErrorBag();
    }

    private function fillFormFromClient($client)
    {
        $this->name = $client->name;
        $this->type = $client->type;
        $this->email = $client->email;
        $this->NPWP = $client->NPWP;
        $this->KPP = $client->KPP;
        $this->logo = $client->logo;
        $this->status = $client->status;
        $this->EFIN = $client->EFIN;
        $this->account_representative = $client->account_representative;
        $this->ar_phone_number = $client->ar_phone_number;
        $this->person_in_charge = $client->person_in_charge;
        $this->address = $client->address;
    }

    private function loadRelationshipData()
    {
        $this->availableOwners = Client::where('type', 'individual')
            ->when($this->editingClient, function ($query) {
                return $query->where('id', '!=', $this->editingClient->id);
            })
            ->select('id', 'name')
            ->get()
            ->toArray();

        $this->availableCompanies = Client::where('type', 'company')
            ->when($this->editingClient, function ($query) {
                return $query->where('id', '!=', $this->editingClient->id);
            })
            ->select('id', 'name')
            ->get()
            ->toArray();
    }

    private function loadCurrentRelationships()
    {
        if ($this->editingClient) {
            if ($this->editingClient->type === 'individual') {
                $this->selectedCompanies = $this->editingClient->ownedCompanies->pluck('id')->toArray();
            } else {
                $this->selectedOwners = $this->editingClient->owners->pluck('id')->toArray();
            }
        }
    }

    // Utility Methods
    public function formatCurrency($amount)
    {
        return 'Rp ' . number_format((float)$amount, 0, ',', '.');
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        
        unset($this->clients);
    }

    public function toggleSelectAll()
    {
        if (count($this->selectedClients) === $this->clients->count()) {
            $this->selectedClients = [];
        } else {
            $this->selectedClients = $this->clients->pluck('id')->toArray();
        }
    }

    // Livewire lifecycle hooks
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterType()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingType()
    {
        // Reset relationships when type changes
        $this->selectedOwners = [];
        $this->selectedCompanies = [];
    }

    public function render()
    {
        return view('livewire.client-management');
    }
}