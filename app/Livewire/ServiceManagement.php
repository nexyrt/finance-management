<?php

namespace App\Livewire;

use App\Models\Service;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Computed;
use Flux\Livewire\Facades\Flux;
use Illuminate\Support\Facades\Log;

class ServiceManagement extends Component
{
    use WithPagination;

    // Properties for service creation/editing
    #[Rule('required|string|max:255')]
    public $name = '';

    #[Rule('required|numeric|min:0')]
    public $price = '';

    #[Rule('required|in:Perizinan,Administrasi Perpajakan,Digital Marketing,Sistem Digital')]
    public $type = '';

    // Filter state
    public $search = '';
    public $typeFilter = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';

    // Edit state
    public $editMode = false;
    public $serviceId = null;

    // Modal visibility
    public $showServiceFormModal = false;
    public $showDeleteConfirmModal = false;
    public $showBulkDeleteConfirmModal = false;
    public $serviceToDeleteId = null;

    // Force delete state
    public $affectedClients = [];
    public $forceDelete = false;

    // Bulk delete
    public $selectedServices = [];
    public $selectAll = false;
    public $bulkAffectedClients = [];
    public $bulkForceDelete = false;

    // Define service types for select options
    public $serviceTypes = [
        ['value' => 'Perizinan', 'label' => 'Perizinan'],
        ['value' => 'Administrasi Perpajakan', 'label' => 'Administrasi Perpajakan'],
        ['value' => 'Digital Marketing', 'label' => 'Digital Marketing'],
        ['value' => 'Sistem Digital', 'label' => 'Sistem Digital'],
    ];

    // Initialize lifecycle hook
    public function mount()
    {
        Log::info('ServiceManagement component mounted');
        // Set default type on mount
        $this->type = 'Perizinan';
    }

    // Reset pagination when filters change
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedTypeFilter()
    {
        $this->resetPage();
    }

    // Toggle select all services
    public function updatedSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedServices = $this->services->pluck('id')->map(fn($id) => (string) $id)->toArray();
            Log::info('All services selected', ['count' => count($this->selectedServices)]);
        } else {
            $this->selectedServices = [];
            Log::info('All services deselected');
        }
    }

    // Check if all services are selected
    public function updatedSelectedServices()
    {
        $this->selectAll = count($this->selectedServices) === $this->services->count();
        Log::info('Selected services updated', [
            'selected_count' => count($this->selectedServices),
            'is_all_selected' => $this->selectAll
        ]);
    }

    // Sort functionality
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        Log::info('Sorting services', [
            'field' => $this->sortField,
            'direction' => $this->sortDirection
        ]);
    }

    // Open create service modal
    public function createService()
    {
        $this->reset(['name', 'price', 'serviceId', 'editMode']);
        $this->type = 'Perizinan'; // Explicitly set default after reset
        $this->showServiceFormModal = true;

        Log::info('Opening create service modal', [
            'initial_type' => $this->type
        ]);
    }

    // Open edit service modal
    public function editService($serviceId)
    {
        Log::info('Editing service', ['service_id' => $serviceId]);

        try {
            $service = Service::findOrFail($serviceId);
            $this->serviceId = $service->id;
            $this->name = $service->name;
            $this->price = $service->price;
            
            // Explicitly set the type and log it
            $this->type = $service->type;
            Log::info('Setting type for edit', ['service_type' => $service->type]);
            
            $this->editMode = true;
            $this->showServiceFormModal = true;

            Log::info('Service found and loaded for editing', [
                'service_id' => $service->id,
                'service_name' => $service->name,
                'service_type' => $service->type,
                'component_type' => $this->type
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading service for edit', [
                'service_id' => $serviceId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            session()->flash('error', 'Error loading service: ' . $e->getMessage());
        }
    }

    // Handle type change - add additional logging
    public function updatedType($value)
    {
        Log::info('Type updated', [
            'old_value' => $this->type ?? 'empty',
            'new_value' => $value
        ]);
        
        // Ensure the property is explicitly set
        $this->type = $value;
    }

    // Open delete confirmation modal
    public function confirmDelete($serviceId)
    {
        Log::info('Confirming delete for service', ['service_id' => $serviceId]);

        $this->serviceToDeleteId = $serviceId;
        $this->forceDelete = false;
        $this->affectedClients = [];

        try {
            // Get the affected clients
            $service = Service::findOrFail($serviceId);

            // Get service clients with their related client and invoice information
            $serviceClients = $service->serviceClients()
                ->with(['client', 'invoiceItems.invoice'])
                ->get();

            // Prepare the affected clients data
            foreach ($serviceClients as $serviceClient) {
                // Get invoice information if available
                $invoices = [];
                foreach ($serviceClient->invoiceItems as $invoiceItem) {
                    if ($invoiceItem->invoice) {
                        $invoices[] = [
                            'id' => $invoiceItem->invoice->id,
                            'number' => $invoiceItem->invoice->invoice_number,
                            'status' => $invoiceItem->invoice->status,
                            'amount' => $invoiceItem->amount,
                        ];
                    }
                }

                $this->affectedClients[] = [
                    'id' => $serviceClient->client->id,
                    'name' => $serviceClient->client->name,
                    'service_date' => $serviceClient->service_date->format('d/m/Y'),
                    'amount' => $serviceClient->amount,
                    'invoices' => $invoices,
                ];
            }

            Log::info('Found affected clients for delete operation', [
                'service_id' => $serviceId,
                'affected_clients_count' => count($this->affectedClients)
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting affected clients', [
                'service_id' => $serviceId,
                'error' => $e->getMessage()
            ]);

            $this->affectedClients = [];
        }

        $this->showDeleteConfirmModal = true;
    }

    // Open bulk delete confirmation modal
    public function confirmBulkDelete()
    {
        Log::info('Confirming bulk delete', ['selected_count' => count($this->selectedServices)]);

        if (count($this->selectedServices) > 0) {
            $this->bulkForceDelete = false;
            $this->bulkAffectedClients = [];

            try {
                // Get all selected services with their service clients
                $services = Service::whereIn('id', $this->selectedServices)
                    ->with(['serviceClients.client', 'serviceClients.invoiceItems.invoice'])
                    ->get();

                // Prepare the affected clients by service
                foreach ($services as $service) {
                    $serviceAffectedClients = [];

                    foreach ($service->serviceClients as $serviceClient) {
                        // Get invoice information if available
                        $invoices = [];
                        foreach ($serviceClient->invoiceItems as $invoiceItem) {
                            if ($invoiceItem->invoice) {
                                $invoices[] = [
                                    'id' => $invoiceItem->invoice->id,
                                    'number' => $invoiceItem->invoice->invoice_number,
                                    'status' => $invoiceItem->invoice->status,
                                    'amount' => $invoiceItem->amount,
                                ];
                            }
                        }

                        $serviceAffectedClients[] = [
                            'id' => $serviceClient->client->id,
                            'name' => $serviceClient->client->name,
                            'service_date' => $serviceClient->service_date->format('d/m/Y'),
                            'amount' => $serviceClient->amount,
                            'invoices' => $invoices,
                        ];
                    }

                    if (count($serviceAffectedClients) > 0) {
                        $this->bulkAffectedClients[$service->id] = [
                            'service_name' => $service->name,
                            'service_type' => $service->type,
                            'affected_clients' => $serviceAffectedClients,
                        ];
                    }
                }

                Log::info('Found affected clients for bulk delete', [
                    'services_count' => count($this->selectedServices),
                    'services_with_clients' => count($this->bulkAffectedClients)
                ]);
            } catch (\Exception $e) {
                Log::error('Error getting affected clients for bulk delete', [
                    'selected_services' => $this->selectedServices,
                    'error' => $e->getMessage()
                ]);

                $this->bulkAffectedClients = [];
            }

            $this->showBulkDeleteConfirmModal = true;
        } else {
            Log::warning('Attempted bulk delete with no services selected');

            session()->flash('error', 'Please select at least one service to delete.');
        }
    }

    // Save service (create or update)
    public function saveService()
    {
        // Log pre-validation data
        Log::info('Pre-validation service data', [
            'name' => $this->name,
            'price' => $this->price,
            'type' => $this->type,
            'editMode' => $this->editMode
        ]);

        $this->validate();
        
        // Add extra debugging to check type value after validation
        Log::info('Type value after validation:', ['type' => $this->type]);

        try {
            // Prepare service data for create/update
            $serviceData = [
                'name' => $this->name,
                'price' => $this->price,
                'type' => $this->type,
            ];

            // Log validated service data
            Log::info('Validated service data', [
                'serviceData' => $serviceData,
                'editMode' => $this->editMode
            ]);

            if ($this->editMode) {
                Log::info('Updating service', [
                    'service_id' => $this->serviceId,
                    'service_data' => $serviceData
                ]);

                $service = Service::find($this->serviceId);
                $service->update($serviceData);

                Log::info('Service updated successfully', [
                    'service_id' => $service->id,
                    'service_name' => $service->name,
                    'service_type' => $service->type
                ]);

                session()->flash('message', 'Service updated successfully!');
            } else {
                Log::info('Creating new service', [
                    'service_data' => $serviceData
                ]);

                $service = Service::create($serviceData);

                Log::info('Service created successfully', [
                    'service_id' => $service->id,
                    'service_name' => $service->name,
                    'service_type' => $service->type
                ]);

                session()->flash('message', 'Service created successfully!');
            }

            $this->showServiceFormModal = false;
            
            // Reset form fields after modal is closed
            $this->reset(['name', 'price', 'serviceId', 'editMode']);
            // Set default type for next time
            $this->type = 'Perizinan';

        } catch (\Exception $e) {
            Log::error('Error saving service', [
                'mode' => $this->editMode ? 'edit' : 'create',
                'service_id' => $this->editMode ? $this->serviceId : null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            session()->flash('error', 'Error saving service: ' . $e->getMessage());
        }
    }

    // Delete service
    public function deleteService()
    {
        Log::info('Attempting to delete service', [
            'service_id' => $this->serviceToDeleteId,
            'force_delete' => $this->forceDelete
        ]);

        try {
            // Find the service using the ID instead of using an object
            $service = Service::findOrFail($this->serviceToDeleteId);

            Log::info('Service found for deletion', [
                'service_id' => $service->id,
                'service_name' => $service->name
            ]);

            // Check if this service is used in any ServiceClient records
            $clientCount = $service->serviceClients()->count();
            Log::info('Checking service client dependencies', [
                'service_id' => $service->id,
                'client_count' => $clientCount
            ]);

            if ($clientCount > 0 && !$this->forceDelete) {
                Log::warning('Cannot delete service - has client dependencies and force delete is off', [
                    'service_id' => $service->id,
                    'client_count' => $clientCount
                ]);

                session()->flash('error', 'This service cannot be deleted because it is assigned to clients. Use force delete to remove anyway.');
            } else {
                // If force delete is enabled, delete all service-client relationships first
                if ($this->forceDelete && $clientCount > 0) {
                    Log::warning('Force deleting service with client dependencies', [
                        'service_id' => $service->id,
                        'client_count' => $clientCount
                    ]);

                    // Detach all invoice items related to this service
                    foreach ($service->serviceClients as $serviceClient) {
                        $serviceClient->invoiceItems()->delete();
                    }

                    // Delete service client records
                    $service->serviceClients()->delete();
                }

                // Perform the deletion
                Log::info('Deleting service', [
                    'service_id' => $service->id,
                    'service_name' => $service->name
                ]);

                $service->delete();

                Log::info('Service deleted successfully', [
                    'service_id' => $service->id
                ]);

                session()->flash('message', 'Service deleted successfully!');
            }
        } catch (\Exception $e) {
            Log::error('Error deleting service', [
                'service_id' => $this->serviceToDeleteId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            session()->flash('error', 'Error deleting service: ' . $e->getMessage());
        }

        $this->showDeleteConfirmModal = false;
        $this->serviceToDeleteId = null;
        $this->forceDelete = false;
        $this->affectedClients = [];
    }

    // Bulk delete services
    public function bulkDeleteServices()
    {
        Log::info('Attempting bulk delete', [
            'selected_count' => count($this->selectedServices),
            'force_delete' => $this->bulkForceDelete,
            'selected_ids' => $this->selectedServices
        ]);

        try {
            // Get all selected services
            $services = Service::whereIn('id', $this->selectedServices)->get();

            Log::info('Retrieved services for bulk delete', [
                'retrieved_count' => $services->count(),
                'service_ids' => $services->pluck('id')
            ]);

            // Check if any services are being used by clients
            $usedServices = [];
            foreach ($services as $service) {
                $clientCount = $service->serviceClients()->count();

                if ($clientCount > 0 && !$this->bulkForceDelete) {
                    $usedServices[] = $service->name;
                    Log::warning('Service has client dependencies and will be skipped', [
                        'service_id' => $service->id,
                        'service_name' => $service->name,
                        'client_count' => $clientCount
                    ]);
                }
            }

            if (count($usedServices) > 0 && !$this->bulkForceDelete) {
                Log::warning('Cannot bulk delete all services - some have client dependencies', [
                    'used_services' => $usedServices
                ]);

                session()->flash('error', 'Some services cannot be deleted because they are assigned to clients: ' . implode(', ', $usedServices));
            } else {
                // If force delete is enabled, delete all dependencies first
                if ($this->bulkForceDelete) {
                    Log::warning('Force deleting services with dependencies', [
                        'selected_count' => count($this->selectedServices)
                    ]);

                    foreach ($services as $service) {
                        // Delete invoice items related to this service
                        foreach ($service->serviceClients as $serviceClient) {
                            $serviceClient->invoiceItems()->delete();
                        }

                        // Delete service client records
                        $service->serviceClients()->delete();
                    }
                }

                // Delete all selected services
                $deleteCount = Service::whereIn('id', $this->selectedServices)->delete();

                Log::info('Services deleted in bulk', [
                    'delete_count' => $deleteCount,
                    'service_ids' => $this->selectedServices
                ]);

                session()->flash('message', count($this->selectedServices) . ' services deleted successfully!');

                // Reset selected services
                $this->selectedServices = [];
                $this->selectAll = false;
            }
        } catch (\Exception $e) {
            Log::error('Error bulk deleting services', [
                'selected_count' => count($this->selectedServices),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            session()->flash('error', 'Error deleting services: ' . $e->getMessage());
        }

        $this->showBulkDeleteConfirmModal = false;
        $this->bulkForceDelete = false;
        $this->bulkAffectedClients = [];
    }

    // Cancel service form
    public function cancelServiceForm()
    {
        Log::info('Service form canceled');
        $this->showServiceFormModal = false;
        $this->reset(['name', 'price', 'serviceId', 'editMode']);
        // Reset type to default
        $this->type = 'Perizinan';
    }

    // Get services for each type
    #[Computed]
    public function services()
    {
        Log::debug('Fetching services with filters', [
            'search' => $this->search,
            'type_filter' => $this->typeFilter,
            'sort_field' => $this->sortField,
            'sort_direction' => $this->sortDirection
        ]);

        return Service::when($this->search, function ($query) {
            $query->where('name', 'like', '%' . $this->search . '%');
        })
            ->when($this->typeFilter, function ($query) {
                $query->where('type', $this->typeFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);
    }

    // Get service count by type for statistics
    #[Computed]
    public function serviceStats()
    {
        $stats = [
            'total' => Service::count(),
            'perizinan' => Service::where('type', 'Perizinan')->count(),
            'administrasi' => Service::where('type', 'Administrasi Perpajakan')->count(),
            'digital_marketing' => Service::where('type', 'Digital Marketing')->count(),
            'sistem_digital' => Service::where('type', 'Sistem Digital')->count(),
        ];

        Log::debug('Service statistics calculated', $stats);

        return $stats;
    }

    public function render()
    {
        Log::debug('ServiceManagement component rendering');
        return view('livewire.service-management');
    }
}