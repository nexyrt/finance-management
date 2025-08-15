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
                ['index' => 'name', 'label' => 'Klien'],
                ['index' => 'type', 'label' => 'Tipe'],
                ['index' => 'person_in_charge', 'label' => 'Info Kontak'],
                ['index' => 'status', 'label' => 'Status'],
                ['index' => 'invoices_count', 'label' => 'Faktur', 'sortable' => false],
                ['index' => 'financial_summary', 'label' => 'Keuangan', 'sortable' => false],
                ['index' => 'actions', 'label' => 'Aksi', 'sortable' => false],
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
            $this->dialog()->warning('Peringatan', 'Tidak ada klien yang dipilih')->send();
            return;
        }

        $count = count($this->selected);
        $this->dialog()
            ->question('Konfirmasi Hapus', "Apakah Anda yakin ingin menghapus {$count} klien? Tindakan ini tidak dapat dibatalkan.")
            ->confirm('Hapus', 'confirmBulkDelete', 'Data berhasil dihapus')
            ->cancel('Batal', 'cancelBulkDelete', 'Operasi dibatalkan')
            ->send();
    }

    public function confirmBulkDelete(string $message)
    {
        $count = Client::whereIn('id', $this->selected)->count();
        Client::whereIn('id', $this->selected)->delete();
        
        $this->selected = [];
        $this->dialog()->success('Berhasil', $message)->send();
        $this->dispatch('client-deleted');
    }

    public function cancelBulkDelete(string $message)
    {
        $this->dialog()->info('Dibatalkan', $message)->send();
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