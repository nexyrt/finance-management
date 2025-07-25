<?php

namespace App\Livewire\Invoices;

use App\Models\Invoice;
use App\Models\Client;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class Index extends Component
{
    use WithPagination, Interactions;

    protected $listeners = [
        'invoice-created' => '$refresh',
        'invoice-updated' => '$refresh', 
        'invoice-deleted' => '$refresh',
        'invoice-sent' => '$refresh',
        'invoice-status-updated' => '$refresh',
    ];

    public $selected = [];
    public $sort = ['column' => 'created_at', 'direction' => 'desc'];
    public ?int $quantity = 10;
    public ?string $search = null;
    public ?string $statusFilter = null;
    public ?string $clientFilter = null;
    public ?string $dateFilter = null;
    public $dateRange = [];

    // Bulk action state - simplified for delete only
    public $bulkActionOptions = []; // Not needed anymore

    public function with(): array
    {
        return [
            'headers' => [
                ['index' => 'invoice_number', 'label' => 'No. Invoice'],
                ['index' => 'client', 'label' => 'Klien'],
                ['index' => 'issue_date', 'label' => 'Tanggal'],
                ['index' => 'due_date', 'label' => 'Jatuh Tempo'],
                ['index' => 'total_amount', 'label' => 'Total'],
                ['index' => 'status', 'label' => 'Status'],
                ['index' => 'payment_status', 'label' => 'Pembayaran', 'sortable' => false],
                ['index' => 'actions', 'label' => 'Aksi', 'sortable' => false],
            ],
            'rows' => Invoice::query()
                ->with(['client:id,name,type', 'payments:id,invoice_id,amount'])
                ->when($this->search, function (Builder $query) {
                    return $query->where('invoice_number', 'like', "%{$this->search}%")
                        ->orWhereHas('client', fn($q) => $q->where('name', 'like', "%{$this->search}%"));
                })
                ->when($this->statusFilter, fn($query) => $query->where('status', $this->statusFilter))
                ->when($this->clientFilter, fn($query) => $query->where('billed_to_id', $this->clientFilter))
                ->when($this->dateRange, function($query) {
                    if (count($this->dateRange) === 2) {
                        return $query->whereBetween('issue_date', $this->dateRange);
                    }
                })
                ->orderBy($this->sort['column'] === 'client' ? 'billed_to_id' : $this->sort['column'], $this->sort['direction'])
                ->paginate($this->quantity)
                ->withQueryString(),
            'clients' => Client::where('status', 'Active')->get(['id', 'name'])->map(fn($client) => [
                'label' => $client->name,
                'value' => $client->id
            ])->toArray(),
            'statusOptions' => [
                ['label' => '📝 Draft', 'value' => 'draft'],
                ['label' => '📤 Terkirim', 'value' => 'sent'],
                ['label' => '💰 Lunas', 'value' => 'paid'],
                ['label' => '💳 Sebagian Lunas', 'value' => 'partially_paid'],
                ['label' => '⏰ Terlambat', 'value' => 'overdue'],
            ]
        ];
    }

    public function render()
    {
        return view('livewire.invoices.index', $this->with());
    }

    public function clearFilters()
    {
        $this->search = null;
        $this->statusFilter = null;
        $this->clientFilter = null;
        $this->dateRange = [];
        $this->resetPage();
    }

    public function clearSelection()
    {
        $this->selected = [];
    }

    // Bulk Delete - simplified
    public function bulkDelete()
    {
        if (empty($this->selected)) {
            $this->dialog()->warning('Peringatan', 'Tidak ada invoice yang dipilih')->send();
            return;
        }

        $count = count($this->selected);
        $this->dialog()
            ->question('Konfirmasi Hapus', "Apakah Anda yakin ingin menghapus {$count} invoice? Tindakan ini tidak dapat dibatalkan.")
            ->confirm('Hapus', 'confirmBulkDelete', 'Data berhasil dihapus')
            ->cancel('Batal', 'cancelBulkDelete', 'Operasi dibatalkan')
            ->send();
    }

    public function confirmBulkDelete(string $message)
    {
        // Only delete draft invoices for safety
        $count = Invoice::whereIn('id', $this->selected)
            ->where('status', 'draft')
            ->count();
            
        Invoice::whereIn('id', $this->selected)
            ->where('status', 'draft')
            ->delete();
        
        $this->selected = [];
        $this->dialog()->success('Berhasil', "Berhasil menghapus {$count} invoice draft")->send();
        $this->dispatch('invoice-deleted');
    }

    public function cancelBulkDelete(string $message)
    {
        $this->dialog()->info('Dibatalkan', $message)->send();
    }

    // Computed properties for dashboard stats
    public function getStatsProperty()
    {
        $baseQuery = Invoice::query();
        
        if ($this->search || $this->statusFilter || $this->clientFilter || $this->dateRange) {
            $baseQuery = $this->applyFiltersToQuery($baseQuery);
        }

        return [
            'total_invoices' => $baseQuery->count(),
            'total_amount' => $baseQuery->sum('total_amount'),
            'paid_amount' => $baseQuery->where('status', 'paid')->sum('total_amount'),
            'overdue_count' => $baseQuery->where('status', 'overdue')->count(),
            'draft_count' => $baseQuery->where('status', 'draft')->count(),
        ];
    }

    private function applyFiltersToQuery($query)
    {
        return $query->when($this->search, function (Builder $query) {
                return $query->where('invoice_number', 'like', "%{$this->search}%")
                    ->orWhereHas('client', fn($q) => $q->where('name', 'like', "%{$this->search}%"));
            })
            ->when($this->statusFilter, fn($query) => $query->where('status', $this->statusFilter))
            ->when($this->clientFilter, fn($query) => $query->where('billed_to_id', $this->clientFilter))
            ->when($this->dateRange, function($query) {
                if (count($this->dateRange) === 2) {
                    return $query->whereBetween('issue_date', $this->dateRange);
                }
            });
    }

    // Property updaters
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedClientFilter()
    {
        $this->resetPage();
    }

    public function updatedDateRange()
    {
        $this->resetPage();
    }
}