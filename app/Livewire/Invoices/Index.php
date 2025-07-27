<?php

namespace App\Livewire\Invoices;

use App\Models\Invoice;
use App\Models\Client;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class Index extends Component
{
    use WithPagination, Interactions;

    // Tab management
    public string $activeTab = 'invoices';
    
    // Table properties
    public array $selected = [];
    // Sort property harus public dan dengan default values yang proper
    public array $sort = ['column' => 'invoice_number', 'direction' => 'asc'];
    public ?int $quantity = 10;
    
    // Filters
    public ?string $search = null;
    public ?string $statusFilter = null;
    public ?string $clientFilter = null;

    public function with(): array
    {
        // Calculate stats
        $stats = $this->calculateStats();
        
        return [
            'headers' => [
                ['index' => 'invoice_number', 'label' => 'No. Invoice'],
                ['index' => 'client_name', 'label' => 'Klien'],
                ['index' => 'issue_date', 'label' => 'Tanggal'],
                ['index' => 'due_date', 'label' => 'Jatuh Tempo'],
                ['index' => 'total_amount', 'label' => 'Jumlah'],
                ['index' => 'status', 'label' => 'Status'],
                ['index' => 'actions', 'label' => 'Aksi', 'sortable' => false],
            ],
            'rows' => $this->getInvoices(),
            'clients' => Client::select('id', 'name')->orderBy('name')->get(),
            'stats' => $stats,
        ];
    }

    private function getInvoices()
    {
        $query = Invoice::query()
            ->join('clients', 'invoices.billed_to_id', '=', 'clients.id')
            ->leftJoin('payments', 'invoices.id', '=', 'payments.invoice_id')
            ->select([
                'invoices.*',
                'clients.name as client_name',
                'clients.type as client_type',
                \DB::raw('COALESCE(SUM(payments.amount), 0) as amount_paid')
            ])
            ->groupBy([
                'invoices.id', 'invoices.invoice_number', 'invoices.billed_to_id', 
                'invoices.total_amount', 'invoices.issue_date', 'invoices.due_date', 
                'invoices.status', 'invoices.created_at', 'invoices.updated_at',
                'clients.name', 'clients.type'
            ]);

        // Apply filters
        if ($this->search) {
            $query->where(function($q) {
                $q->where('invoices.invoice_number', 'like', "%{$this->search}%")
                  ->orWhere('clients.name', 'like', "%{$this->search}%");
            });
        }

        if ($this->statusFilter) {
            $query->where('invoices.status', $this->statusFilter);
        }

        if ($this->clientFilter) {
            $query->where('invoices.billed_to_id', $this->clientFilter);
        }

        // Handle sorting - TallStackUI akan otomatis apply sorting via array_values($this->sort)
        if ($this->sort['column'] === 'client_name') {
            $query->orderBy('clients.name', $this->sort['direction']);
        } elseif (in_array($this->sort['column'], ['invoice_number', 'issue_date', 'due_date', 'total_amount', 'status'])) {
            $query->orderBy('invoices.' . $this->sort['column'], $this->sort['direction']);
        } else {
            // Default sorting jika tidak ada custom handling
            $query->orderBy(...array_values($this->sort));
        }

        return $query->paginate($this->quantity)->withQueryString();
    }

    private function calculateStats(): array
    {
        $baseQuery = Invoice::query();
        
        return [
            'total_invoices' => $baseQuery->count(),
            'outstanding_amount' => $baseQuery->whereIn('status', ['sent', 'overdue', 'partially_paid'])
                ->sum('total_amount') - 
                \DB::table('payments')
                    ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
                    ->whereIn('invoices.status', ['sent', 'overdue', 'partially_paid'])
                    ->sum('payments.amount'),
            'paid_this_month' => \DB::table('payments')
                ->whereMonth('payment_date', now()->month)
                ->whereYear('payment_date', now()->year)
                ->sum('amount'),
            'overdue_count' => $baseQuery->where('status', 'overdue')->count(),
        ];
    }

    public function clearFilters(): void
    {
        $this->search = null;
        $this->statusFilter = null;
        $this->clientFilter = null;
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedClientFilter(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.invoices.index', $this->with());
    }
}