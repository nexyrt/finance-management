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
    public $editingClient = null;
    public $isEditing = false;
    public $deletedInvoices = [];

    // Form data
    public $form = [
        'name' => '',
        'type' => 'individual',
        'email' => '',
        'phone' => '',
        'address' => '',
        'tax_id' => '',
        'relationships' => [],
    ];

    protected $rules = [
        'form.name' => 'required|string|max:255',
        'form.type' => 'required|in:individual,company',
        'form.email' => 'nullable|email|max:255',
        'form.phone' => 'nullable|string|max:20',
        'form.address' => 'nullable|string',
        'form.tax_id' => 'nullable|string|max:50',
        'form.relationships' => 'array',
    ];

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

    public function getAvailableConnectionsProperty()
    {
        if (empty($this->form['type'])) {
            return collect();
        }

        if ($this->form['type'] === 'individual') {
            $currentId = $this->editingClient?->id;
            return Client::companies()
                ->when($currentId, fn($query) => $query->where('id', '!=', $currentId))
                ->get(['id', 'name', 'email']);
        } else {
            $currentId = $this->editingClient?->id;
            return Client::individuals()
                ->when($currentId, fn($query) => $query->where('id', '!=', $currentId))
                ->get(['id', 'name', 'email']);
        }
    }

    public function resetRelationships()
    {
        $this->form['relationships'] = [];
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->isEditing = false;
    }

    public function openEditModal($clientId)
    {
        $client = Client::findOrFail($clientId);
        $this->editingClient = $client;
        $this->isEditing = true;

        // Ensure the type is properly set for the select component
        $this->form = [
            'name' => $client->name,
            'type' => $client->type, // This will be the correct value
            'email' => $client->email,
            'phone' => $client->phone,
            'address' => $client->address,
            'tax_id' => $client->tax_id,
            'relationships' => $this->getCurrentRelationships($client),
        ];
    }

    public function save()
    {
        try {
            $this->validate();

            DB::transaction(function () {
                if ($this->isEditing) {
                    $this->editingClient->update($this->form);
                    $client = $this->editingClient;
                } else {
                    $client = Client::create($this->form);
                }

                $this->updateRelationships($client);
            });

            $this->resetForm();

            session()->flash('message', $this->isEditing ? 'Client updated successfully.' : 'Client created successfully.');

            // Emit event to close modal
            $this->js('$dispatch("close-modal", { name: "client-form" })');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation errors are handled by Livewire automatically
            throw $e;
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while saving the client: ' . $e->getMessage());
        }
    }

    public function deleteClient($clientId)
    {
        try {
            $client = Client::find($clientId);
            if ($client) {
                DB::transaction(function () use ($client) {
                    $client->delete();
                });

                session()->flash('message', 'Client deleted successfully.');
                $this->js('$dispatch("close-modal", { name: "delete-modal" })');
                $this->js('$dispatch("clients-deleted")');
            } else {
                session()->flash('error', 'Client not found.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting client: ' . $e->getMessage());
        }
    }

    public function deleteMultiple(array $clientIds)
    {
        try {
            if (empty($clientIds)) {
                session()->flash('error', 'No clients selected for deletion.');
                return;
            }

            $deletedCount = 0;
            $invoiceCount = 0;

            DB::transaction(function () use ($clientIds, &$deletedCount, &$invoiceCount) {
                foreach ($clientIds as $clientId) {
                    $client = Client::with('invoices')->find($clientId);
                    if ($client) {
                        $invoiceCount += $client->invoices->count();
                        $client->delete();
                        $deletedCount++;
                    }
                }
            });

            $message = sprintf(
                'Successfully deleted %d client%s',
                $deletedCount,
                $deletedCount === 1 ? '' : 's'
            );

            if ($invoiceCount > 0) {
                $message .= sprintf(' and %d invoice%s', $invoiceCount, $invoiceCount === 1 ? '' : 's');
            }

            $message .= '.';

            session()->flash('message', $message);

            $this->js('$dispatch("close-modal", { name: "delete-modal" })');
            $this->js('$dispatch("clients-deleted")');
        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting clients: ' . $e->getMessage());
        }
    }

    public function getDeletedInvoices(array $clientIds)
    {
        if (empty($clientIds))
            return [];

        return Client::whereIn('id', $clientIds)
            ->with('invoices')
            ->get()
            ->flatMap(function ($client) {
                return $client->invoices;
            })
            ->map(function ($invoice) {
                return [
                    'invoice_number' => $invoice->invoice_number,
                    'total_amount' => $invoice->total_amount,
                    'status' => $invoice->status,
                ];
            })
            ->toArray();
    }

    public function getClientDetails($clientId)
    {
        $client = Client::with(['invoices', 'ownedCompanies', 'owners'])->findOrFail($clientId);

        return [
            'id' => $client->id,
            'name' => $client->name,
            'type' => $client->type,
            'email' => $client->email,
            'phone' => $client->phone,
            'address' => $client->address,
            'tax_id' => $client->tax_id,
            'owned_companies' => $client->ownedCompanies->map(function ($company) {
                return [
                    'id' => $company->id,
                    'name' => $company->name,
                    'email' => $company->email
                ];
            }),
            'owners' => $client->owners->map(function ($owner) {
                return [
                    'id' => $owner->id,
                    'name' => $owner->name,
                    'email' => $owner->email
                ];
            }),
            'invoices' => $client->invoices
        ];
    }

    protected function getCurrentRelationships($client)
    {
        if ($client->type === 'individual') {
            return $client->ownedCompanies()->pluck('company_id')->toArray();
        } else {
            return $client->owners()->pluck('owner_id')->toArray();
        }
    }

    protected function updateRelationships($client)
    {
        if ($client->type === 'individual') {
            $currentRelations = $client->ownedCompanies()->pluck('company_id')->toArray();
            $newRelations = $this->form['relationships'] ?? [];

            foreach (array_diff($currentRelations, $newRelations) as $companyId) {
                ClientRelationship::where('owner_id', $client->id)
                    ->where('company_id', $companyId)
                    ->delete();
            }

            foreach (array_diff($newRelations, $currentRelations) as $companyId) {
                ClientRelationship::create([
                    'owner_id' => $client->id,
                    'company_id' => $companyId,
                ]);
            }
        } else {
            $currentRelations = $client->owners()->pluck('owner_id')->toArray();
            $newRelations = $this->form['relationships'] ?? [];

            foreach (array_diff($currentRelations, $newRelations) as $ownerId) {
                ClientRelationship::where('company_id', $client->id)
                    ->where('owner_id', $ownerId)
                    ->delete();
            }

            foreach (array_diff($newRelations, $currentRelations) as $ownerId) {
                ClientRelationship::create([
                    'owner_id' => $ownerId,
                    'company_id' => $client->id,
                ]);
            }
        }
    }

    public function toggleRelationship($id)
    {
        $relationships = $this->form['relationships'] ?? [];

        if (in_array($id, $relationships)) {
            $this->form['relationships'] = array_values(array_diff($relationships, [$id]));
        } else {
            $this->form['relationships'][] = $id;
        }
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
            'relationships' => [],
        ];
        $this->editingClient = null;
    }

    public function render()
    {
        return view('livewire.client-management');
    }
}