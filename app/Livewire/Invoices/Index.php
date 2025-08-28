<?php

namespace App\Livewire\Invoices;

use App\Models\Invoice;
use App\Models\Client;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use TallStackUi\Traits\Interactions;
use Illuminate\Pagination\LengthAwarePaginator;

class Index extends Component
{
    use WithPagination, Interactions;

    protected $listeners = [
        'invoice-updated' => '$refresh',
        'payment-created' => '$refresh', 
        'invoice-payment-updated' => '$refresh',
        'invoice-created' => '$refresh',
    ];

    // Table properties
    public array $selected = [];
    public array $sort = ['column' => 'invoice_number', 'direction' => 'desc'];
    public ?int $quantity = 10;
    
    public array $headers = [
        ['index' => 'invoice_number', 'label' => 'No. Invoice'],
        ['index' => 'client_name', 'label' => 'Klien'],
        ['index' => 'issue_date', 'label' => 'Tanggal'],
        ['index' => 'due_date', 'label' => 'Jatuh Tempo'],
        ['index' => 'total_amount', 'label' => 'Jumlah'],
        ['index' => 'status', 'label' => 'Status'],
        ['index' => 'actions', 'label' => 'Aksi', 'sortable' => false],
    ];

    // Filters
    public ?string $search = null;
    public ?string $statusFilter = null;
    public ?string $clientFilter = null;
    public $dateRange = [];

    // Modal properties
    public bool $showBulkDeleteModal = false;

    public function mount()
    {
        $this->dateRange = [];
    }

    public function createInvoice(): void
    {
        $this->dispatch('create-invoice');
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
                \DB::raw('COALESCE(SUM(payments.amount), 0) as amount_paid')
            ])
            ->groupBy([
                'invoices.id', 'invoices.invoice_number', 'invoices.billed_to_id',
                'invoices.total_amount', 'invoices.issue_date', 'invoices.due_date',
                'invoices.status', 'invoices.created_at', 'invoices.updated_at',
                'invoices.subtotal', 'invoices.discount_amount', 'invoices.discount_type',
                'invoices.discount_value', 'invoices.discount_reason',
                'clients.name', 'clients.type'
            ]);

        // Apply filters
        if ($this->search) {
            $query->where(function ($q) {
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

        if (!empty($this->dateRange) && is_array($this->dateRange) && count($this->dateRange) >= 2 && $this->dateRange[0] && $this->dateRange[1]) {
            $query->whereDate('invoices.issue_date', '>=', $this->dateRange[0])
                ->whereDate('invoices.issue_date', '<=', $this->dateRange[1]);
        }

        // Handle sorting
        if ($this->sort['column'] === 'client_name') {
            $query->orderBy('clients.name', $this->sort['direction']);
        } elseif (in_array($this->sort['column'], ['invoice_number', 'issue_date', 'due_date', 'total_amount', 'status'])) {
            $query->orderBy('invoices.' . $this->sort['column'], $this->sort['direction']);
        } else {
            $query->orderBy(...array_values($this->sort));
        }

        return $query->paginate($this->quantity)->withQueryString();
    }

    public function bulkPrintInvoices()
    {
        if (empty($this->selected)) {
            $this->toast()->warning('Warning', 'Pilih minimal satu invoice untuk di-print')->send();
            return;
        }

        try {
            $invoices = Invoice::whereIn('id', $this->selected)->get();

            if ($invoices->isEmpty()) {
                $this->toast()->error('Error', 'Invoice yang dipilih tidak ditemukan')->send();
                return;
            }

            $downloadUrls = [];
            foreach ($invoices as $invoice) {
                $downloadUrls[] = [
                    'invoice_number' => $invoice->invoice_number,
                    'url' => route('invoice.pdf.download', $invoice->id)
                ];
            }

            $this->dispatch('start-bulk-download', [
                'urls' => $downloadUrls,
                'delay' => 1000
            ]);

            $this->toast()
                ->success('Bulk Download Started', "Memulai download {$invoices->count()} invoice PDF")
                ->send();

            $this->selected = [];

        } catch (\Exception $e) {
            $this->toast()->error('Error', 'Gagal memulai bulk download: ' . $e->getMessage())->send();
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
        $this->search = null;
        $this->statusFilter = null;
        $this->clientFilter = null;
        $this->dateRange = [];
        $this->resetPage();
    }

    public function openBulkDeleteModal(): void
    {
        if (empty($this->selected)) {
            $this->toast()->warning('Warning', 'Pilih minimal satu invoice untuk dihapus')->send();
            return;
        }

        $this->showBulkDeleteModal = true;
    }

    public function bulkDelete(): void
    {
        if (empty($this->selected)) {
            $this->toast()->warning('Warning', 'Pilih minimal satu invoice untuk dihapus')->send();
            return;
        }

        try {
            $invoices = Invoice::with(['payments'])->whereIn('id', $this->selected)->get();
            $deletedCount = 0;

            foreach ($invoices as $invoice) {
                try {
                    if ($invoice->payments->count() > 0) {
                        $invoice->payments()->delete();
                    }

                    $invoice->delete();
                    $deletedCount++;

                } catch (\Exception $e) {
                    \Log::error("Failed to delete invoice {$invoice->invoice_number}: " . $e->getMessage());
                }
            }

            $this->selected = [];
            $this->showBulkDeleteModal = false;

            if ($deletedCount > 0) {
                $this->dialog()
                    ->success('Bulk Delete Selesai', "Berhasil menghapus {$deletedCount} invoice")
                    ->send();
            } else {
                $this->toast()->error('Error', 'Tidak ada invoice yang berhasil dihapus')->send();
            }

        } catch (\Exception $e) {
            $this->toast()->error('Error', 'Gagal melakukan bulk delete: ' . $e->getMessage())->send();
        }
    }

    public function exportExcel()
    {
        $service = new \App\Services\InvoiceExportService();

        return $service->exportExcel([
            'search' => $this->search,
            'statusFilter' => $this->statusFilter,
            'clientFilter' => $this->clientFilter,
            'dateRange' => $this->dateRange,
        ]);
    }

    public function exportPdf()
    {
        $service = new \App\Services\InvoiceExportService();

        return response()->streamDownload(function () use ($service) {
            echo $service->exportPdf([
                'search' => $this->search,
                'statusFilter' => $this->statusFilter,
                'clientFilter' => $this->clientFilter,
                'dateRange' => $this->dateRange,
            ])->output();
        }, 'invoices-' . now()->format('Y-m-d') . '.pdf', [
            'Content-Type' => 'application/pdf'
        ]);
    }

    // Filter update methods
    public function updatedStatusFilter(): void { $this->resetPage(); }
    public function updatedClientFilter(): void { $this->resetPage(); }
    public function updatedDateRange(): void { $this->resetPage(); }
    public function updatedSearch(): void { $this->resetPage(); }

    private function getStats(): array
    {
        $baseQuery = Invoice::query();
        
        // Calculate total revenue and COGS
        $totalRevenue = $baseQuery->sum('total_amount');
        $totalCogs = \DB::table('invoice_items')->sum('cogs_amount');
        $totalProfit = $totalRevenue - $totalCogs;

        // Calculate outstanding vs paid profit
        $totalPaidAmount = \DB::table('payments')->sum('amount');
        $outstandingAmount = $totalRevenue - $totalPaidAmount;
        
        // Simple ratio calculation for outstanding profit
        $profitRatio = $totalRevenue > 0 ? $totalProfit / $totalRevenue : 0;
        $outstandingProfit = $outstandingAmount * $profitRatio;
        $paidProfit = $totalPaidAmount * $profitRatio;

        return [
            'total_revenue' => $totalRevenue,
            'total_cogs' => $totalCogs,
            'total_profit' => $totalProfit,
            'profit_margin' => $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0,
            'outstanding_profit' => $outstandingProfit,
            'paid_profit' => $paidProfit,
            'paid_this_month' => \DB::table('payments')
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