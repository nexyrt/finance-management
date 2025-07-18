<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class Index extends Component
{
    use WithPagination;
    use Interactions;

    // Table properties
    public array $selected = [];
    public array $sort = [
        'column' => 'name',
        'direction' => 'asc',
    ];

    // Filter properties
    public ?int $quantity = 10;
    public ?string $search = null;
    public ?string $typeFilter = null;
    public ?string $statusFilter = null;

    // Actions
    public function toggleClientStatus($clientId)
    {
        $client = Client::findOrFail($clientId);
        $client->update([
            'status' => $client->status === 'Active' ? 'Inactive' : 'Active'
        ]);

        $this->dialog()->success('Success', 'Client status updated successfully.')->send();
    }

    public function deleteClient($clientId)
    {
        $this->dialog()
            ->question('Delete Client', 'Are you sure you want to delete this client? This action cannot be undone.')
            ->confirm('Delete', 'performDelete', ['clientId' => $clientId])
            ->cancel('Cancel')
            ->send();
    }

    public function performDelete($params)
    {
        $clientId = $params['clientId'];
        $client = Client::findOrFail($clientId);

        // Check if client has invoices
        if ($client->invoices()->exists()) {
            $this->dialog()
                ->error('Cannot Delete!', 'Client has existing invoices and cannot be deleted.')
                ->send();
            return;
        }

        $client->delete();

        $this->dialog()
            ->success('Success', 'Client deleted successfully.')
            ->send();
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
                ['index' => 'person_in_charge', 'label' => 'Contact Info'],
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
                ->with([
                    'invoices' => function ($query) {
                        $query->select('id', 'billed_to_id', 'total_amount', 'status');
                    }
                ])
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