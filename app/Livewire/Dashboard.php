<?php

namespace App\Livewire;
use Livewire\Component;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Client;
use App\Models\BankAccount;
use Carbon\Carbon;

class Dashboard extends Component
{
    /**
     * Calculate total revenue from paid invoices
     */
    public function calculateTotalRevenue()
    {
        return Payment::sum('amount');
    }

    /**
     * Calculate revenue for current month
     */
    public function getCurrentMonthRevenue()
    {
        return Payment::whereMonth('payment_date', Carbon::now()->month)
            ->whereYear('payment_date', Carbon::now()->year)
            ->sum('amount');
    }

    /**
     * Calculate revenue for last month
     */
    public function getLastMonthRevenue()
    {
        return Payment::whereMonth('payment_date', Carbon::now()->subMonth()->month)
            ->whereYear('payment_date', Carbon::now()->subMonth()->year)
            ->sum('amount');
    }

    /**
     * Calculate revenue growth percentage
     */
    public function getRevenueGrowth()
    {
        $currentMonthTotal = $this->getCurrentMonthRevenue();
        $lastMonthTotal = $this->getLastMonthRevenue();

        if ($lastMonthTotal == 0)
            return 0;

        return round((($currentMonthTotal - $lastMonthTotal) / $lastMonthTotal) * 100, 1);
    }

    /**
     * Calculate outstanding invoices amount
     */
    public function getOutstandingInvoices()
    {
        return Invoice::whereIn('status', ['sent', 'partially_paid', 'overdue'])
            ->sum('total_amount') -
            Payment::whereHas('invoice', function ($query) {
                $query->whereIn('status', ['sent', 'partially_paid', 'overdue']);
            })->sum('amount');
    }

    /**
     * Get total clients count
     */
    public function getTotalClients()
    {
        return Client::where('status', 'Active')->count();
    }

    /**
     * Get new clients this month
     */
    public function getNewClientsThisMonth()
    {
        return Client::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
    }

    /**
     * Calculate total bank balance
     */
    public function getTotalBankBalance()
    {
        return BankAccount::sum('current_balance');
    }

    /**
     * Get top earning clients
     */
    public function getTopEarningClients()
    {
        return Client::withSum('invoiceItems', 'amount')
            ->orderBy('invoice_items_sum_amount', 'desc')
            ->take(4)
            ->get()
            ->map(function ($client, $index) {
                return [
                    'rank' => $index + 1,
                    'name' => $client->name,
                    'type' => $client->type,
                    'total_revenue' => $client->invoice_items_sum_amount ?? 0,
                    'growth' => rand(-5, 20) // Temporary - would need historical data
                ];
            });
    }

    /**
     * Get revenue by service type
     */
    public function getRevenueByService()
    {
        $serviceRevenue = Payment::join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->selectRaw('invoice_items.service_name, SUM(payments.amount) as total_revenue')
            ->groupBy('invoice_items.service_name')
            ->orderBy('total_revenue', 'desc')
            ->take(5)
            ->get();

        return $serviceRevenue->pluck('total_revenue', 'service_name')->toArray();
    }

    /** 
     * Get monthly revenue data for chart
     */
    public function getMonthlyRevenue()
    {
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $revenue = Payment::whereMonth('payment_date', $month->month)
                ->whereYear('payment_date', $month->year)
                ->sum('amount');

            $monthlyData[] = [
                'month' => $month->format('M'),
                'revenue' => $revenue
            ];
        }

        return $monthlyData;
    }

    /**
     * Format currency to Indonesian Rupiah
     */
    public function formatCurrency($amount)
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    /**
     * Format large numbers to millions
     */
    public function formatToMillions($amount)
    {
        return number_format($amount / 1000000, 1) . 'M';
    }

    public function render()
    {
        return view('livewire.dashboard', [
            'totalRevenue' => $this->formatCurrency($this->calculateTotalRevenue()),
            'revenueGrowth' => $this->getRevenueGrowth(),
            'outstanding' => $this->formatCurrency($this->getOutstandingInvoices()),
            'totalClients' => $this->getTotalClients(),
            'newClientsThisMonth' => $this->getNewClientsThisMonth(),
            'totalBankBalance' => $this->formatCurrency($this->getTotalBankBalance()),
            'topEarningClients' => $this->getTopEarningClients(),
            'revenueByService' => $this->getRevenueByService(),
            'monthlyRevenue' => $this->getMonthlyRevenue(),
        ]);
    }
}
