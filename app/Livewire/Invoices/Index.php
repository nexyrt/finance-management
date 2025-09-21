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
    public $dateRange = [];

    #[On('filter-changed')]
    public function updateFilters(array $filters): void
    {
        $this->statusFilter = $filters['statusFilter'];
        $this->clientFilter = $filters['clientFilter'];
        $this->selectedMonth = $filters['selectedMonth'];
        $this->dateRange = $filters['dateRange'];
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
        // Base query with filters applied
        $invoiceQuery = Invoice::query()
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->clientFilter, fn($q) => $q->where('billed_to_id', $this->clientFilter))
            // Date filtering - range overrides month
            ->when(
                $this->dateRange && count($this->dateRange) >= 2 && $this->dateRange[0] && $this->dateRange[1],
                fn($q) => $q->whereBetween('issue_date', [
                    $this->dateRange[0],
                    $this->dateRange[1]
                ])
            )
            ->unless(
                $this->dateRange,
                fn($q) => $q->when(
                    $this->selectedMonth,
                    fn($query) => $query->whereYear('issue_date', substr($this->selectedMonth, 0, 4))
                        ->whereMonth('issue_date', substr($this->selectedMonth, 5, 2))
                )
            );

        // Get invoices with their items
        $invoices = $invoiceQuery->with('items')->get();

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

            if ($totalPaid <= $itemCogs) {
                return $grossProfit;
            }

            $realizedProfit = $totalPaid - $itemCogs;
            return max(0, $grossProfit - $realizedProfit);
        });

        // Calculate paid profit
        $paidProfit = $totalProfit - $outstandingProfit;

        return [
            'total_revenue' => $netRevenue,
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
        return view('livewire.invoices.index');
    }
}