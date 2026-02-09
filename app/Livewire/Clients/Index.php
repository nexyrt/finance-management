<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class Index extends Component
{
    use WithPagination, Interactions;

    protected $listeners = [
        'client-created' => 'refresh', 
        'client-deleted' => 'refresh', 
        'client-updated' => 'refresh',
    ];

    public array $selected = [];
    public array $sort = ['column' => 'name', 'direction' => 'asc'];
    public ?int $quantity = 10;
    public ?string $search = null;
    public ?string $typeFilter = null;
    public ?string $statusFilter = null;

    public function with(): array
    {
        return [
            'headers' => [
                ['index' => 'name', 'label' => __('common.name')],
                ['index' => 'type', 'label' => __('common.type')],
                ['index' => 'person_in_charge', 'label' => __('pages.contact_info')],
                ['index' => 'status', 'label' => __('common.status')],
                ['index' => 'invoices_count', 'label' => __('common.invoices'), 'sortable' => false],
                ['index' => 'financial_summary', 'label' => __('pages.financial'), 'sortable' => false],
                ['index' => 'actions', 'label' => __('common.actions'), 'sortable' => false],
            ],
            'rows' => Client::query()
                ->when($this->search, function (Builder $query) {
                    return $query->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhere('NPWP', 'like', "%{$this->search}%");
                })
                ->when($this->typeFilter, fn($query) => $query->where('type', $this->typeFilter))
                ->when($this->statusFilter, fn($query) => $query->where('status', $this->statusFilter))
                ->withCount('invoices')
                ->with(['invoices' => fn($query) => $query->select('id', 'billed_to_id', 'total_amount', 'status')])
                ->orderBy(...array_values($this->sort))
                ->paginate($this->quantity)
                ->withQueryString()
        ];
    }

    public function render()
    {
        return view('livewire.clients.index', $this->with());
    }

    public function clearFilters()
    {
        $this->search = null;
        $this->typeFilter = null;
        $this->statusFilter = null;
        $this->resetPage();
    }

    public function bulkDelete()
    {
        if (empty($this->selected)) {
            $this->dialog()->warning(__('common.warning'), __('pages.no_clients_selected'))->send();
            return;
        }

        $count = count($this->selected);
        $this->dialog()
            ->question(__('pages.confirm_delete'), __('pages.confirm_delete_clients', ['count' => $count]))
            ->confirm(__('common.delete'), 'confirmBulkDelete', __('common.deleted_successfully'))
            ->cancel(__('common.cancel'), 'cancelBulkDelete', __('pages.operation_cancelled'))
            ->send();
    }

    public function confirmBulkDelete(string $message)
    {
        $count = Client::whereIn('id', $this->selected)->count();
        Client::whereIn('id', $this->selected)->delete();

        $this->selected = [];
        $this->dialog()->success(__('common.success'), $message)->send();
        $this->dispatch('client-deleted');
    }

    public function cancelBulkDelete(string $message)
    {
        $this->dialog()->info(__('pages.cancelled'), $message)->send();
    }

    public function clearSelection()
    {
        $this->selected = [];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedTypeFilter()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }
}