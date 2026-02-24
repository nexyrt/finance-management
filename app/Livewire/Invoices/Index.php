<?php

namespace App\Livewire\Invoices;

use App\Models\Invoice;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    protected $listeners = [
        'invoice-updated' => '$refresh',
        'payment-created' => '$refresh',
        'invoice-payment-updated' => '$refresh',
        'invoice-created' => '$refresh',
    ];

    // Filter state from Listing component
    public ?string $statusFilter = null;
    public ?string $clientFilter = null;
    public ?string $selectedMonth = null;
    public $search = '';
    public $dateRange = [];

    #[On('filter-changed')]
    public function updateFilters(array $filters): void
    {
        $this->statusFilter = $filters['statusFilter'];
        $this->clientFilter = $filters['clientFilter'];
        $this->selectedMonth = $filters['selectedMonth'];
        $this->dateRange = $filters['dateRange'];
        $this->search = $filters['search'] ?? '';
    }

    #[On('invoice-sent')]
    #[On('invoice-deleted')]
    public function refreshStats(): void
    {
        unset($this->stats);
    }

    #[Computed]
    public function stats(): array
    {
        // Base filtered invoice IDs subquery (reused across all aggregations)
        $filteredIds = Invoice::query()
            ->join('clients', 'invoices.billed_to_id', '=', 'clients.id')
            ->select('invoices.id')
            ->when($this->statusFilter, fn($q) => $q->where('invoices.status', $this->statusFilter))
            ->when($this->clientFilter, fn($q) => $q->where('invoices.billed_to_id', $this->clientFilter))
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('invoices.invoice_number', 'like', '%' . $this->search . '%')
                        ->orWhere('clients.name', 'like', '%' . $this->search . '%');
                });
            })
            ->when(
                $this->dateRange && count($this->dateRange) >= 2 && $this->dateRange[0] && $this->dateRange[1],
                fn($q) => $q->whereBetween('invoices.issue_date', [
                    $this->dateRange[0],
                    $this->dateRange[1]
                ])
            )
            ->unless(
                $this->dateRange,
                fn($q) => $q->when(
                    $this->selectedMonth,
                    fn($query) => $query->whereYear('invoices.issue_date', substr($this->selectedMonth, 0, 4))
                        ->whereMonth('invoices.issue_date', substr($this->selectedMonth, 5, 2))
                )
            );

        // Basic stats: count + total revenue (1 query)
        $basicStats = DB::table('invoices')
            ->whereIn('id', $filteredIds)
            ->selectRaw('COUNT(*) as invoice_count, COALESCE(SUM(total_amount), 0) as total_revenue')
            ->first();

        $invoiceCount = $basicStats->invoice_count;
        $totalRevenue = (int) $basicStats->total_revenue;

        // Item-level stats: tax deposits + COGS (1 query)
        $itemStats = DB::table('invoice_items')
            ->whereIn('invoice_id', $filteredIds)
            ->selectRaw('
                COALESCE(SUM(CASE WHEN is_tax_deposit = 1 THEN amount ELSE 0 END), 0) as total_tax_deposits,
                COALESCE(SUM(CASE WHEN is_tax_deposit = 0 THEN cogs_amount ELSE 0 END), 0) as total_cogs
            ')
            ->first();

        $totalTaxDeposits = (int) $itemStats->total_tax_deposits;
        $totalCogs = (int) $itemStats->total_cogs;
        $totalProfit = $totalRevenue - $totalTaxDeposits - $totalCogs;
        $profitMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;

        // Outstanding profit: per-invoice calculation for unpaid invoices (1 query, lightweight)
        $outstandingData = DB::table('invoices')
            ->whereIn('invoices.id', $filteredIds)
            ->where('invoices.status', '!=', 'paid')
            ->leftJoin(DB::raw('(SELECT invoice_id, SUM(amount) as total_paid FROM payments GROUP BY invoice_id) as p'), 'invoices.id', '=', 'p.invoice_id')
            ->leftJoin(DB::raw('(SELECT invoice_id,
                SUM(CASE WHEN is_tax_deposit = 1 THEN amount ELSE 0 END) as tax_deposits,
                SUM(CASE WHEN is_tax_deposit = 0 THEN cogs_amount ELSE 0 END) as item_cogs
                FROM invoice_items GROUP BY invoice_id) as ii'), 'invoices.id', '=', 'ii.invoice_id')
            ->select([
                'invoices.total_amount',
                DB::raw('COALESCE(p.total_paid, 0) as total_paid'),
                DB::raw('COALESCE(ii.tax_deposits, 0) as tax_deposits'),
                DB::raw('COALESCE(ii.item_cogs, 0) as item_cogs'),
            ])
            ->get();

        $outstandingProfit = (int) $outstandingData->sum(function ($row) {
            $invoiceProfit = $row->total_amount - $row->tax_deposits - $row->item_cogs;
            if ($invoiceProfit <= 0) {
                return 0;
            }

            if ($row->total_paid <= ($row->item_cogs + $row->tax_deposits)) {
                return $invoiceProfit;
            }

            $realizedProfit = min($row->total_paid - ($row->item_cogs + $row->tax_deposits), $invoiceProfit);

            return max(0, $invoiceProfit - $realizedProfit);
        });

        $paidProfit = $totalProfit - $outstandingProfit;

        // Payments this month (1 query, unfiltered)
        $paidThisMonth = DB::table('payments')
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');

        return [
            'total_revenue' => $totalRevenue,
            'total_cogs' => $totalCogs,
            'total_profit' => $totalProfit,
            'profit_margin' => $profitMargin,
            'outstanding_profit' => $outstandingProfit,
            'paid_profit' => $paidProfit,
            'paid_this_month' => (int) $paidThisMonth,
            'invoice_count' => $invoiceCount,
            'average_invoice_value' => $invoiceCount > 0 ? $totalRevenue / $invoiceCount : 0,
        ];
    }

    public function render()
    {
        return view('livewire.invoices.index');
    }
}