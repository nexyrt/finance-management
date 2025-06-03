<?php

namespace App\Livewire;

use App\Models\Service;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ServiceManagement extends Component
{
    use WithPagination;

    // Form Properties
    #[Rule('required|string|max:255')]
    public $name = '';

    #[Rule('required|numeric|min:0')]
    public $price = '';

    #[Rule('required|in:Perizinan,Administrasi Perpajakan,Digital Marketing,Sistem Digital')]
    public $type = 'Perizinan';

    // Filter & Sort Properties
    public $search = '';
    public $typeFilter = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';

    // State Properties
    public $editMode = false;
    public $serviceId = null;
    public $selectedServices = [];
    public $selectAll = false;

    // Lifecycle Methods
    public function mount()
    {
        $this->resetForm();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedTypeFilter()
    {
        $this->resetPage();
    }

    public function updatedSelectAll()
    {
        $services = $this->getServices();
        
        if ($this->selectAll) {
            $this->selectedServices = $services->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selectedServices = [];
        }
    }

    // Sort Methods
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    // CRUD Methods
    public function createService()
    {
        $this->resetForm();
        $this->editMode = false;
        Flux::modal('service-form')->show();
    }

    public function editService($serviceId)
    {
        try {
            $service = Service::findOrFail($serviceId);
            
            // Fill form with service data
            $this->serviceId = $service->id;
            $this->name = $service->name;
            $this->price = $service->price;
            $this->type = $service->type;
            $this->editMode = true;
            
            Flux::modal('service-form')->show();
            Log::info('Service loaded for editing', ['service_id' => $serviceId]);
        } catch (\Exception $e) {
            Log::error('Error loading service: ' . $e->getMessage());
            session()->flash('error', 'Service not found.');
        }
    }

    public function saveService()
    {
        try {
            $this->validate();

            DB::transaction(function () {
                $serviceData = [
                    'name' => $this->name,
                    'price' => $this->price,
                    'type' => $this->type,
                ];

                if ($this->editMode && $this->serviceId) {
                    // Update existing service
                    $service = Service::findOrFail($this->serviceId);
                    $service->update($serviceData);
                    $message = 'Service updated successfully!';
                    Log::info('Service updated', ['service_id' => $service->id]);
                } else {
                    // Create new service
                    $service = Service::create($serviceData);
                    $message = 'Service created successfully!';
                    Log::info('Service created', ['service_id' => $service->id]);
                }

                session()->flash('success', $message);
            });

            $this->closeModal();
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error saving service: ' . $e->getMessage());
            session()->flash('error', 'Failed to save service. Please try again.');
        }
    }

    public function confirmDelete($serviceId)
    {
        $this->serviceId = $serviceId;
        Flux::modal('delete-confirm')->show();
    }

    public function deleteService()
    {
        try {
            DB::transaction(function () {
                $service = Service::findOrFail($this->serviceId);
                $service->delete();
                Log::info('Service deleted', ['service_id' => $this->serviceId]);
            });

            session()->flash('success', 'Service deleted successfully!');
            $this->closeDeleteModal();
        } catch (\Exception $e) {
            Log::error('Error deleting service: ' . $e->getMessage());
            session()->flash('error', 'Failed to delete service. Please try again.');
            $this->closeDeleteModal();
        }
    }

    public function confirmBulkDelete()
    {
        if (empty($this->selectedServices)) {
            session()->flash('error', 'Please select services to delete.');
            return;
        }
        
        Flux::modal('bulk-delete-confirm')->show();
    }

    public function bulkDeleteServices()
    {
        try {
            DB::transaction(function () {
                $deletedCount = Service::whereIn('id', $this->selectedServices)->delete();
                Log::info('Bulk delete completed', ['deleted_count' => $deletedCount]);
            });

            session()->flash('success', count($this->selectedServices) . ' services deleted successfully!');
            $this->selectedServices = [];
            $this->selectAll = false;
            $this->closeDeleteModal();
        } catch (\Exception $e) {
            Log::error('Error bulk deleting services: ' . $e->getMessage());
            session()->flash('error', 'Failed to delete services. Please try again.');
            $this->closeDeleteModal();
        }
    }

    // Helper Methods
    public function resetForm()
    {
        $this->name = '';
        $this->price = '';
        $this->type = 'Perizinan';
        $this->serviceId = null;
        $this->editMode = false;
        $this->resetErrorBag();
    }

    public function closeModal()
    {
        Flux::modal('service-form')->close();
        $this->resetForm();
    }

    private function closeDeleteModal()
    {
        Flux::modal('delete-confirm')->close();
        Flux::modal('bulk-delete-confirm')->close();
        $this->serviceId = null;
    }

    // Data Methods
    private function getServices()
    {
        return Service::when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->typeFilter, function ($query) {
                $query->where('type', $this->typeFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);
    }

    private function getServiceStats()
    {
        return [
            'total' => Service::count(),
            'perizinan' => Service::where('type', 'Perizinan')->count(),
            'administrasi' => Service::where('type', 'Administrasi Perpajakan')->count(),
            'digital_marketing' => Service::where('type', 'Digital Marketing')->count(),
            'sistem_digital' => Service::where('type', 'Sistem Digital')->count(),
        ];
    }

    private function getServiceTypes()
    {
        return [
            ['value' => 'Perizinan', 'label' => 'Perizinan'],
            ['value' => 'Administrasi Perpajakan', 'label' => 'Administrasi Perpajakan'],
            ['value' => 'Digital Marketing', 'label' => 'Digital Marketing'],
            ['value' => 'Sistem Digital', 'label' => 'Sistem Digital'],
        ];
    }

    public function render()
    {
        return view('livewire.service-management', [
            'services' => $this->getServices(),
            'serviceStats' => $this->getServiceStats(),
            'serviceTypes' => $this->getServiceTypes(),
        ]);
    }
}