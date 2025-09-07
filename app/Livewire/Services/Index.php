<?php

namespace App\Livewire\Services;

use TallStackUi\Traits\Interactions;
use App\Models\Service;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class Index extends Component
{
    use WithPagination, Interactions;

    public array $selected = [];
    public array $sort = ['column' => 'created_at', 'direction' => 'desc'];
    public ?int $quantity = 10;
    public ?string $search = null;
    public ?string $typeFilter = null;

    public array $headers = [
        ['index' => 'name', 'label' => 'Nama Layanan'],
        ['index' => 'type', 'label' => 'Kategori'],
        ['index' => 'price', 'label' => 'Harga', 'sortable' => true],
        ['index' => 'created_at', 'label' => 'Dibuat'],
        ['index' => 'actions', 'label' => 'Aksi', 'sortable' => false],
    ];

    public function edit($serviceId): void
    {
        $this->dispatch('load::service', Service::find($serviceId));
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'typeFilter']);
        $this->resetPage();
    }

    public function bulkDelete(): void
    {
        if (empty($this->selected)) {
            $this->toast()->warning('Warning', 'Pilih minimal satu layanan untuk dihapus')->send();
            return;
        }

        $this->toast()
            ->question('Hapus Layanan?', count($this->selected) . ' layanan akan dihapus permanen')
            ->confirm(method: 'confirmBulkDelete')
            ->cancel()
            ->send();
    }

    public function confirmBulkDelete(): void
    {
        try {
            $deletedCount = count($this->selected);
            Service::whereIn('id', $this->selected)->delete();
            $this->selected = [];
            $this->toast()->success('Berhasil', "Berhasil menghapus {$deletedCount} layanan")->send();
        } catch (\Exception $e) {
            $this->toast()->error('Error', 'Gagal menghapus: ' . $e->getMessage())->send();
        }
    }

    #[Computed]
    public function services(): LengthAwarePaginator
    {
        return Service::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->typeFilter, fn($q) => $q->where('type', $this->typeFilter))
            ->orderBy(...array_values($this->sort))
            ->paginate($this->quantity)
            ->withQueryString();
    }

    #[Computed]
    public function stats(): array
    {
        $services = Service::all();

        return [
            'total_services' => $services->count(),
            'avg_price' => $services->avg('price'),
            'highest_price' => $services->max('price'),
            'by_type' => $services->groupBy('type')->map->count(),
        ];
    }

    public function render()
    {
        return view('livewire.services.index', [
            'stats' => $this->stats,
        ]);
    }
}