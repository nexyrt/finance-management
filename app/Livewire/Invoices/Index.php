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
        // Base query with filters applied (same as Listing component)
        $invoiceQuery = Invoice::query()
            ->join('clients', 'invoices.billed_to_id', '=', 'clients.id')
            ->select('invoices.*')
            ->when($this->statusFilter, fn($q) => $q->where('invoices.status', $this->statusFilter))
            ->when($this->clientFilter, fn($q) => $q->where('invoices.billed_to_id', $this->clientFilter))
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('invoices.invoice_number', 'like', '%' . $this->search . '%')
                        ->orWhere('clients.name', 'like', '%' . $this->search . '%');
                });
            })
            // Date filtering - range overrides month
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

        // Get invoices with their items and payments
        $invoices = $invoiceQuery->with(['items', 'payments'])->get();

        // Total Revenue: sum of all invoice total_amount (already includes discount calculation)
        $totalRevenue = $invoices->sum('total_amount');

        // Total Tax Deposits: sum of tax deposit amounts (not real revenue)
        $totalTaxDeposits = $invoices->sum(function ($invoice) {
            return $invoice->items->where('is_tax_deposit', true)->sum('amount');
        });

        // Total COGS: sum of cogs_amount from invoice items (excluding tax deposits)
        $totalCogs = $invoices->sum(function ($invoice) {
            return $invoice->items->where('is_tax_deposit', false)->sum('cogs_amount');
        });

        // Total Profit: Revenue - Tax Deposits - COGS
        $totalProfit = $totalRevenue - $totalTaxDeposits - $totalCogs;

        // Calculate profit margin
        $profitMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;

        // Calculate outstanding profit (profit from unpaid/partially paid invoices)
        $outstandingProfit = $invoices->sum(function ($invoice) {
            // Get COGS for this invoice (excluding tax deposits)
            $itemCogs = $invoice->items->where('is_tax_deposit', false)->sum('cogs_amount');

            // Use invoice total_amount directly (already includes discount)
            $invoiceRevenue = $invoice->total_amount;

            // Calculate profit for this invoice
            $invoiceProfit = $invoiceRevenue - $itemCogs;

            // If no profit, return 0
            if ($invoiceProfit <= 0) {
                return 0;
            }

            // Calculate how much of this profit is still outstanding
            $totalPaid = $invoice->payments->sum('amount');

            if ($totalPaid <= $itemCogs) {
                // If payment hasn't covered COGS yet, all profit is outstanding
                return $invoiceProfit;
            }

            // Calculate realized profit from payments above COGS
            $realizedProfit = min($totalPaid - $itemCogs, $invoiceProfit);

            // Outstanding profit is what's left
            return max(0, $invoiceProfit - $realizedProfit);
        });

        // Paid profit is the difference
        $paidProfit = $totalProfit - $outstandingProfit;

        // Calculate payments made this month (for all invoices, not filtered)
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
            'paid_this_month' => $paidThisMonth,
            'invoice_count' => $invoices->count(),
            'average_invoice_value' => $invoices->count() > 0 ? $totalRevenue / $invoices->count() : 0,
        ];
    }

    public function render()
    {
        return view('livewire.invoices.index');
    }
}