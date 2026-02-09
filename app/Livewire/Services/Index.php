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

    public function getHeadersProperty(): array
    {
        return [
            ['index' => 'name', 'label' => __('pages.service_name')],
            ['index' => 'type', 'label' => __('common.category')],
            ['index' => 'price', 'label' => __('common.price'), 'sortable' => true],
            ['index' => 'created_at', 'label' => __('common.created_at')],
            ['index' => 'actions', 'label' => __('common.actions'), 'sortable' => false],
        ];
    }

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
            $this->toast()->warning(__('common.warning'), __('pages.no_services_selected'))->send();
            return;
        }

        $this->toast()
            ->question(__('pages.delete_services'), __('pages.confirm_delete_services', ['count' => count($this->selected)]))
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
            $this->toast()->success(__('common.success'), __('pages.services_deleted_successfully', ['count' => $deletedCount]))->send();
        } catch (\Exception $e) {
            $this->toast()->error(__('common.error'), __('pages.delete_failed') . ': ' . $e->getMessage())->send();
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