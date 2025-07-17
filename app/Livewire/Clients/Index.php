<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    // Table properties
    public ?int $quantity = 10;
    public ?string $search = null;
    public array $selected = [];
    public array $sort = [ 
        'column' => 'name',
        'direction' => 'asc',
    ];

    // Filter properties
    public ?string $typeFilter = null;
    public ?string $statusFilter = null;

    // Actions
    public function toggleClientStatus($clientId)
    {
        $client = Client::findOrFail($clientId);
        $client->update([
            'status' => $client->status === 'Active' ? 'Inactive' : 'Active'
        ]);
        
        $this->dispatch('notify', [
            'title' => 'Success!',
            'description' => 'Client status updated successfully.',
            'icon' => 'check'
        ]);
    }

    public function deleteClient($clientId)
    {
        $client = Client::findOrFail($clientId);
        
        // Check if client has invoices
        if ($client->invoices()->exists()) {
            $this->dispatch('notify', [
                'title' => 'Cannot Delete!',
                'description' => 'Client has existing invoices and cannot be deleted.',
                'icon' => 'exclamation-triangle'
            ]);
            return;
        }

        $client->delete();
        
        $this->dispatch('notify', [
            'title' => 'Success!',
            'description' => 'Client deleted successfully.',
            'icon' => 'check'
        ]);
    }

    public function bulkActivate()
    {
        Client::whereIn('id', $this->selected)->update(['status' => 'Active']);
        $this->selected = [];
        
        $this->dispatch('notify', [
            'title' => 'Success!',
            'description' => 'Selected clients have been activated.',
            'icon' => 'check'
        ]);
    }

    public function bulkDeactivate()
    {
        Client::whereIn('id', $this->selected)->update(['status' => 'Inactive']);
        $this->selected = [];
        
        $this->dispatch('notify', [
            'title' => 'Success!',
            'description' => 'Selected clients have been deactivated.',
            'icon' => 'check'
        ]);
    }

    public function bulkDelete()
    {
        // Check for clients with invoices
        $clientsWithInvoices = Client::whereIn('id', $this->selected)
            ->whereHas('invoices')
            ->count();

        if ($clientsWithInvoices > 0) {
            $this->dispatch('notify', [
                'title' => 'Cannot Delete!',
                'description' => 'Some clients have existing invoices and cannot be deleted.',
                'icon' => 'exclamation-triangle'
            ]);
            return;
        }

        Client::whereIn('id', $this->selected)->delete();
        $this->selected = [];
        
        $this->dispatch('notify', [
            'title' => 'Success!',
            'description' => 'Selected clients have been deleted.',
            'icon' => 'check'
        ]);
    }

    public function with(): array
    {
        return [
            'headers' => [
                ['index' => 'name', 'label' => 'Client'],
                ['index' => 'type', 'label' => 'Type'],
                ['index' => 'contact', 'label' => 'Contact Info'],
                ['index' => 'status', 'label' => 'Status'],
                ['index' => 'invoices_count', 'label' => 'Invoices'],
                ['index' => 'financial_summary', 'label' => 'Financial'],
                ['index' => 'account_representative', 'label' => 'Account Rep'],
                ['index' => 'created_at', 'label' => 'Created'],
                ['index' => 'actions', 'label' => 'Actions', 'sortable' => false],
            ],
            'rows' => Client::query()
                ->when($this->search, function (Builder $query) {
                    return $query->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhere('NPWP', 'like', "%{$this->search}%")
                        ->orWhere('account_representative', 'like', "%{$this->search}%");
                })
                ->when($this->typeFilter, function (Builder $query) {
                    return $query->where('type', $this->typeFilter);
                })
                ->when($this->statusFilter, function (Builder $query) {
                    return $query->where('status', $this->statusFilter);
                })
                ->withCount('invoices')
                ->with(['invoices' => function ($query) {
                    $query->select('id', 'billed_to_id', 'total_amount', 'status');
                }])
                ->orderBy(...array_values($this->sort))
                ->paginate($this->quantity)
                ->withQueryString()
        ];
    }

    public function render()
    {
        $clients = $this->with();

        return view('livewire.clients.index', [
            'headers' => $clients['headers'],
            'rows' => $clients['rows'],
        ]);
    }
}