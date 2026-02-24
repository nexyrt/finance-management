<?php

namespace App\Livewire\Services;

use TallStackUi\Traits\Interactions;
use App\Models\Service;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

#[Lazy]
class Index extends Component
{
    use WithPagination, Interactions;

    public array $selected = [];
    public array $sort = ['column' => 'created_at', 'direction' => 'desc'];
    public ?int $quantity = 10;
    public ?string $search = null;
    public ?string $typeFilter = null;

    // Headers diinisialisasi di mount() agar __() translation berfungsi
    public array $headers = [];

    public function placeholder(): View
    {
        return view('livewire.placeholders.table-skeleton');
    }

    public function mount(): void
    {
        $this->headers = [
            ['index' => 'name', 'label' => __('pages.service_name')],
            ['index' => 'type', 'label' => __('common.category')],
            ['index' => 'price', 'label' => __('common.price'), 'sortable' => true],
            ['index' => 'created_at', 'label' => __('common.created_at')],
            ['index' => 'actions', 'label' => __('common.actions'), 'sortable' => false],
        ];
    }

    #[On('service-created')]
    #[On('service-updated')]
    #[On('service-deleted')]
    public function refreshData(): void
    {
        $this->reset('selected');
        unset($this->stats);
    }

    public function edit(int $serviceId): void
    {
        // Dispatch hanya ID — Edit component yang akan query sendiri
        $this->dispatch('load::service', serviceId: $serviceId);
    }

    public function confirmDelete(int $serviceId): void
    {
        $this->dispatch('delete::service', serviceId: $serviceId);
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
            ->question(__('pages.delete_services'), __('pages.confirm_delete_services', ['count' => \count($this->selected)]))
            ->confirm(method: 'confirmBulkDelete')
            ->cancel()
            ->send();
    }

    public function confirmBulkDelete(): void
    {
        try {
            $deletedCount = \count($this->selected);
            Service::whereIn('id', $this->selected)->delete();
            $this->selected = [];
            unset($this->stats);
            $this->toast()->success(__('common.success'), __('pages.services_deleted_successfully', ['count' => $deletedCount]))->send();
        } catch (\Exception $e) {
            $this->toast()->error(__('common.error'), __('pages.delete_failed') . ': ' . $e->getMessage())->send();
        }
    }

    // Opsi filter kategori — data dinamis, gunakan translate_text() di PHP
    #[Computed]
    public function categoryOptions(): array
    {
        $types = ['Perizinan', 'Administrasi Perpajakan', 'Digital Marketing', 'Sistem Digital'];

        return array_map(fn($type) => [
            'label' => translate_text($type),
            'value' => $type,
        ], $types);
    }

    #[Computed]
    public function services(): LengthAwarePaginator
    {
        return Service::query()
            ->select(['id', 'name', 'type', 'price', 'created_at'])
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->typeFilter, fn($q) => $q->where('type', $this->typeFilter))
            ->orderBy(...array_values($this->sort))
            ->paginate($this->quantity)
            ->withQueryString();
    }

    #[Computed]
    public function stats(): array
    {
        // Pakai DB agregat — tidak load semua rows ke memory
        $aggregate = Service::selectRaw('
            COUNT(*) as total,
            AVG(price) as avg_price,
            MAX(price) as highest_price
        ')->toBase()->first();

        $byType = Service::selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->orderByDesc('count')
            ->pluck('count', 'type');

        return [
            'total_services' => (int) ($aggregate->total ?? 0),
            'avg_price'      => $aggregate->avg_price ?? null,
            'highest_price'  => $aggregate->highest_price ?? null,
            'by_type'        => $byType,
        ];
    }

    public function render()
    {
        return view('livewire.services.index', [
            'stats' => $this->stats,
        ]);
    }
}