<?php

namespace App\Livewire\Invoices;

use App\Models\Invoice;
use App\Models\Client;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use TallStackUi\Traits\Interactions;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    use WithPagination, Interactions;

    protected $listeners = [
        'invoice-updated' => '$refresh',
        'payment-created' => '$refresh',
        'invoice-payment-updated' => '$refresh',
        'invoice-created' => '$refresh',
    ];

    public array $selected = [];
    public array $sort = ['column' => 'invoice_number', 'direction' => 'desc'];
    public ?int $quantity = 25;
    public ?string $search = null;
    public ?string $statusFilter = null;
    public ?string $clientFilter = null;
    public $dateRange = [];

    public array $headers = [
        ['index' => 'invoice_number', 'label' => 'No. Invoice'],
        ['index' => 'client_name', 'label' => 'Klien'],
        ['index' => 'issue_date', 'label' => 'Tanggal'],
        ['index' => 'due_date', 'label' => 'Jatuh Tempo'],
        ['index' => 'total_amount', 'label' => 'Jumlah'],
        ['index' => 'status', 'label' => 'Status'],
        ['index' => 'actions', 'label' => 'Aksi', 'sortable' => false],
    ];

    public function mount()
    {
        $this->dateRange = [];
    }

    #[Computed]
    public function invoices(): LengthAwarePaginator
    {
        $query = Invoice::query()
            ->join('clients', 'invoices.billed_to_id', '=', 'clients.id')
            ->leftJoin('payments', 'invoices.id', '=', 'payments.invoice_id')
            ->select([
                'invoices.*',
                'clients.name as client_name',
                'clients.type as client_type',
                DB::raw('COALESCE(SUM(payments.amount), 0) as amount_paid')
            ])
            ->groupBy(array_merge(
                [
                    'invoices.id',
                    'invoices.invoice_number',
                    'invoices.billed_to_id',
                    'invoices.total_amount',
                    'invoices.issue_date',
                    'invoices.due_date',
                    'invoices.status',
                    'invoices.created_at',
                    'invoices.updated_at',
                    'invoices.subtotal',
                    'invoices.discount_amount',
                    'invoices.discount_type',
                    'invoices.discount_value',
                    'invoices.discount_reason'
                ],
                ['clients.name', 'clients.type']
            ));

        // Filters
        $query->when(
            $this->search,
            fn($q) =>
            $q->where('invoices.invoice_number', 'like', "%{$this->search}%")
                ->orWhere('clients.name', 'like', "%{$this->search}%")
        );

        $query->when($this->statusFilter, fn($q) => $q->where('invoices.status', $this->statusFilter));
        $query->when($this->clientFilter, fn($q) => $q->where('invoices.billed_to_id', $this->clientFilter));

        $query->when(
            !empty($this->dateRange) && count($this->dateRange) >= 2 && $this->dateRange[0] && $this->dateRange[1],
            fn($q) => $q->whereDate('invoices.issue_date', '>=', $this->dateRange[0])
                ->whereDate('invoices.issue_date', '<=', $this->dateRange[1])
        );

        // Sorting
        match ($this->sort['column']) {
            'client_name' => $query->orderBy('clients.name', $this->sort['direction']),
            'invoice_number', 'issue_date', 'due_date', 'total_amount', 'status' =>
            $query->orderBy('invoices.' . $this->sort['column'], $this->sort['direction']),
            default => $query->orderBy(...array_values($this->sort))
        };

        return $query->paginate($this->quantity)->withQueryString();
    }

    public function bulkPrintInvoices(): void
    {
        if (empty($this->selected)) {
            $this->toast()->warning('Warning', 'Pilih minimal satu invoice untuk di-print')->send();
            return;
        }

        try {
            $invoices = Invoice::whereIn('id', $this->selected)->get(['id', 'invoice_number']);

            if ($invoices->isEmpty()) {
                $this->toast()->error('Error', 'Invoice tidak ditemukan')->send();
                return;
            }

            // Dispatch ke JavaScript dengan delay yang lebih lama untuk browser modern
            $this->dispatch('start-bulk-download', [
                'downloads' => $invoices->map(fn($invoice) => [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'url' => route('invoice.pdf.download', $invoice->id)
                ])->toArray(),
                'delay' => 2000, // 2 detik delay antar download
                'method' => 'iframe' // Gunakan method iframe untuk bypass popup blocker
            ]);

            $this->selected = [];
            $this->toast()->success('Download Dimulai', "Mengunduh {$invoices->count()} invoice PDF")->send();
        } catch (\Exception $e) {
            $this->toast()->error('Error', 'Gagal memulai download: ' . $e->getMessage())->send();
        }
    }

    public function sendInvoice(int $invoiceId): void
    {
        try {
            $invoice = Invoice::find($invoiceId);

            if (!$invoice) {
                $this->toast()->error('Error', 'Invoice tidak ditemukan')->send();
                return;
            }

            if ($invoice->status !== 'draft') {
                $this->toast()->warning('Warning', 'Hanya invoice draft yang bisa dikirim')->send();
                return;
            }

            $invoice->update(['status' => 'sent']);
            $this->toast()->success('Berhasil', "Invoice {$invoice->invoice_number} berhasil dikirim")->send();
        } catch (\Exception $e) {
            $this->toast()->error('Error', 'Gagal mengirim invoice: ' . $e->getMessage())->send();
        }
    }

    public function clearFilters(): void
    {
        $this->fill(['search' => null, 'statusFilter' => null, 'clientFilter' => null, 'dateRange' => []]);
        $this->resetPage();
    }

    public function bulkDelete(): void
    {
        if (empty($this->selected)) {
            $this->toast()->warning('Warning', 'Pilih minimal satu invoice untuk dihapus')->send();
            return;
        }

        try {
            DB::transaction(function () {
                \App\Models\InvoiceItem::whereIn('invoice_id', $this->selected)->delete();
                \App\Models\Invoice::whereIn('id', $this->selected)->delete();
            });

            $deletedCount = count($this->selected);
            $this->selected = [];
            $this->toast()->success('Berhasil', "Berhasil menghapus {$deletedCount} invoice")->send();
        } catch (\Exception $e) {
            $this->toast()->error('Error', 'Gagal menghapus: ' . $e->getMessage())->send();
        }
    }

    public function exportExcel()
    {
        return (new \App\Services\InvoiceExportService())->exportExcel($this->getFilters());
    }

    public function exportPdf()
    {
        $service = new \App\Services\InvoiceExportService();
        return response()->streamDownload(
            fn() => print $service->exportPdf($this->getFilters())->output(),
            'invoices-' . now()->format('Y-m-d') . '.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }

    private function getFilters(): array
    {
        return [
            'search' => $this->search,
            'statusFilter' => $this->statusFilter,
            'clientFilter' => $this->clientFilter,
            'dateRange' => $this->dateRange,
        ];
    }

    // Reset page on filter updates
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
    public function updatedSearch()
    {
        $this->resetPage();
    }

    private function getStats(): array
    {
        // Get all invoices with their items
        $invoices = Invoice::with('items')->get();

        // Calculate net revenue (excluding tax deposits)
        $netRevenue = $invoices->sum(function ($invoice) {
            return $invoice->items->where('is_tax_deposit', false)->sum('amount');
        });

        // Calculate total COGS (excluding tax deposits)
        $totalCogs = $invoices->sum(function ($invoice) {
            return $invoice->items->where('is_tax_deposit', false)->sum('cogs_amount');
        });

        // Calculate total gross profit (net revenue - cogs - discounts)
        $totalProfit = $invoices->sum(function ($invoice) {
            $itemNetRevenue = $invoice->items->where('is_tax_deposit', false)->sum('amount');
            $itemCogs = $invoice->items->where('is_tax_deposit', false)->sum('cogs_amount');
            return $itemNetRevenue - $itemCogs - ($invoice->discount_amount ?? 0);
        });

        // Calculate outstanding profit (profit from unpaid invoices)
        $outstandingProfit = $invoices->sum(function ($invoice) {
            $itemNetRevenue = $invoice->items->where('is_tax_deposit', false)->sum('amount');
            $itemCogs = $invoice->items->where('is_tax_deposit', false)->sum('cogs_amount');
            $grossProfit = $itemNetRevenue - $itemCogs - ($invoice->discount_amount ?? 0);

            $totalPaid = $invoice->amount_paid;
            $totalAmount = $invoice->total_amount;

            if ($totalPaid <= $itemCogs) {
                return $grossProfit;
            }

            $realizedProfit = $totalPaid - $itemCogs;
            return max(0, $grossProfit - $realizedProfit);
        });

        // Calculate paid profit
        $paidProfit = $totalProfit - $outstandingProfit;

        // Total invoice amount (including tax deposits for billing)
        $totalInvoiceAmount = $invoices->sum('total_amount');

        return [
            'total_revenue' => $netRevenue, // Net revenue excluding tax deposits
            'total_cogs' => $totalCogs,
            'total_profit' => $totalProfit,
            'profit_margin' => $netRevenue > 0 ? ($totalProfit / $netRevenue) * 100 : 0,
            'outstanding_profit' => $outstandingProfit,
            'paid_profit' => $paidProfit,
            'paid_this_month' => DB::table('payments')
                ->whereMonth('payment_date', now()->month)
                ->whereYear('payment_date', now()->year)
                ->sum('amount'),
        ];
    }

    public function render()
    {
        return view('livewire.invoices.index', [
            'clients' => Client::select('id', 'name')->orderBy('name')->get(),
            'stats' => $this->getStats(),
        ]);
    }
}