<?php

namespace App\Livewire;

use App\Models\BankAccount;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    // Filters
    public string $dateRange = '';
    public string $clientFilter = '';
    public string $serviceFilter = '';
    public array $dateRanges = [];
    
    // Dashboard data
    public $totalRevenue;
    public $pendingRevenue;
    public $bankBalances;
    public $recentInvoices;
    public $agingReceivables;
    public $clientRevenue;
    public $serviceRevenue;
    public $upcomingInstallments;
    
    public function mount()
    {
        // Set default date range to current month
        $this->dateRange = Carbon::now()->startOfMonth()->format('Y-m-d') . ' to ' . Carbon::now()->endOfMonth()->format('Y-m-d');
        
        // Set date range options
        $this->dateRanges = [
            'last_30_days' => 'Last 30 Days',
            'this_month' => 'This Month',
            'last_month' => 'Last Month',
            'this_quarter' => 'This Quarter',
            'this_year' => 'This Year',
            'custom' => 'Custom Range'
        ];
        
        // Initial data load
        $this->loadDashboardData();
    }
    
    public function loadDashboardData()
    {
        // Parse date range
        $dates = $this->parseDateRange($this->dateRange);
        $startDate = $dates['start'];
        $endDate = $dates['end'];
        
        // Get financial data
        $this->loadRevenueData($startDate, $endDate);
        $this->loadBankBalances();
        $this->loadRecentInvoices();
        $this->loadAgingReceivables();
        $this->loadClientRevenue($startDate, $endDate);
        $this->loadServiceRevenue($startDate, $endDate);
        $this->loadUpcomingInstallments();
    }
    
    private function parseDateRange($range)
    {
        // Handle the new format: "2025-05-12 to 2025-05-23"
        if (strpos($range, ' to ') !== false) {
            list($start, $end) = explode(' to ', $range);
            return [
                'start' => Carbon::parse($start)->startOfDay(),
                'end' => Carbon::parse($end)->endOfDay(),
            ];
        }
        // Keep legacy format support: "12/05/2025 - 23/05/2025"
        elseif (strpos($range, ' - ') !== false) {
            list($start, $end) = explode(' - ', $range);
            return [
                'start' => Carbon::createFromFormat('d/m/Y', $start)->startOfDay(),
                'end' => Carbon::createFromFormat('d/m/Y', $end)->endOfDay(),
            ];
        }
        
        // Default to current month if format is not recognized
        return [
            'start' => Carbon::now()->startOfMonth(),
            'end' => Carbon::now()->endOfMonth(),
        ];
    }
    
    private function loadRevenueData($startDate, $endDate)
    {
        // Total revenue (paid invoices)
        $this->totalRevenue = Payment::whereBetween('payment_date', [$startDate, $endDate])
            ->sum('amount');
            
        // Pending revenue (unpaid/partially paid invoices)
        $this->pendingRevenue = Invoice::whereIn('status', ['sent', 'partially_paid'])
            ->whereBetween('issue_date', [$startDate, $endDate])
            ->sum('total_amount') - Invoice::whereIn('status', ['partially_paid'])
            ->whereBetween('issue_date', [$startDate, $endDate])
            ->join('payments', 'invoices.id', '=', 'payments.invoice_id')
            ->sum('payments.amount');
    }
    
    private function loadBankBalances()
    {
        $this->bankBalances = BankAccount::select('id', 'account_name', 'bank_name', 'currency', 'current_balance')
            ->get();
    }
    
    private function loadRecentInvoices()
    {
        $this->recentInvoices = Invoice::select('id', 'invoice_number', 'billed_to_id', 'total_amount', 'status')
            ->with(['client' => function($query) {
                $query->select('id', 'name');
            }])
            ->orderByDesc('issue_date')
            ->limit(5)
            ->get();
    }
    
    private function loadAgingReceivables()
    {
        $now = Carbon::now();
        
        $this->agingReceivables = [
            'current' => Invoice::whereIn('status', ['sent', 'partially_paid'])
                ->where('due_date', '>=', $now)
                ->sum('total_amount'),
            '1_30' => Invoice::whereIn('status', ['sent', 'partially_paid'])
                ->whereBetween('due_date', [$now->copy()->subDays(30), $now])
                ->sum('total_amount'),
            '31_60' => Invoice::whereIn('status', ['sent', 'partially_paid'])
                ->whereBetween('due_date', [$now->copy()->subDays(60), $now->copy()->subDays(31)])
                ->sum('total_amount'),
            '61_90' => Invoice::whereIn('status', ['sent', 'partially_paid'])
                ->whereBetween('due_date', [$now->copy()->subDays(90), $now->copy()->subDays(61)])
                ->sum('total_amount'),
            'over_90' => Invoice::whereIn('status', ['sent', 'partially_paid'])
                ->where('due_date', '<', $now->copy()->subDays(90))
                ->sum('total_amount'),
        ];
    }
    
    private function loadClientRevenue($startDate, $endDate)
    {
        try {
            // Use the correct field name (billed_to_id) from the invoices table
            $this->clientRevenue = DB::table('payments')
                ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
                ->join('clients', 'invoices.billed_to_id', '=', 'clients.id')
                ->whereBetween('payments.payment_date', [$startDate, $endDate])
                ->when($this->clientFilter, function($query) {
                    return $query->where('clients.id', $this->clientFilter);
                })
                ->select('clients.id', 'clients.name', DB::raw('SUM(payments.amount) as revenue'))
                ->groupBy('clients.id', 'clients.name')
                ->orderByDesc('revenue')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            // Fallback to empty array if query fails
            $this->clientRevenue = collect([]);
            
            // Log the error for debugging
            \Log::error('Error in loadClientRevenue: ' . $e->getMessage());
        }
    }
    
    private function loadServiceRevenue($startDate, $endDate)
    {
        try {
            // Calculate a proportional amount of the payment for each service
            // This is a complex query because we need to divide payment across invoice items
            $this->serviceRevenue = DB::table('payments')
                ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
                ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
                ->join('service_clients', 'invoice_items.service_client_id', '=', 'service_clients.id')
                ->join('services', 'service_clients.service_id', '=', 'services.id')
                ->whereBetween('payments.payment_date', [$startDate, $endDate])
                ->when($this->serviceFilter, function($query) {
                    return $query->where('services.id', $this->serviceFilter);
                })
                ->select(
                    'services.id', 
                    'services.name',
                    DB::raw('SUM(payments.amount * (invoice_items.amount / invoices.total_amount)) as revenue')
                )
                ->groupBy('services.id', 'services.name')
                ->orderByDesc('revenue')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            // Fallback to simpler query if the complex calculation fails
            try {
                // Simplified query that just assigns full payment to each service
                $this->serviceRevenue = DB::table('services')
                    ->join('service_clients', 'services.id', '=', 'service_clients.service_id')
                    ->join('invoice_items', 'service_clients.id', '=', 'invoice_items.service_client_id')
                    ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
                    ->join('payments', 'invoices.id', '=', 'payments.invoice_id')
                    ->whereBetween('payments.payment_date', [$startDate, $endDate])
                    ->when($this->serviceFilter, function($query) {
                        return $query->where('services.id', $this->serviceFilter);
                    })
                    ->select('services.id', 'services.name', DB::raw('SUM(invoice_items.amount) as revenue'))
                    ->groupBy('services.id', 'services.name')
                    ->orderByDesc('revenue')
                    ->limit(10)
                    ->get();
            } catch (\Exception $innerE) {
                // Fallback to empty collection if both queries fail
                $this->serviceRevenue = collect([]);
                \Log::error('Error in simplified loadServiceRevenue: ' . $innerE->getMessage());
            }
            
            // Log the original error
            \Log::error('Error in loadServiceRevenue: ' . $e->getMessage());
        }
    }
    
    private function loadUpcomingInstallments()
    {
        $this->upcomingInstallments = Invoice::select('id', 'invoice_number', 'billed_to_id', 'total_amount', 'installment_count', 'due_date')
            ->with(['client' => function($query) {
                $query->select('id', 'name');
            }])
            ->where('payment_terms', 'installment')
            ->whereIn('status', ['sent', 'partially_paid'])
            ->orderBy('due_date')
            ->limit(5)
            ->get();
    }
    
    public function updatedDateRange()
    {
        $this->loadDashboardData();
    }
    
    public function updatedClientFilter()
    {
        $this->loadDashboardData();
    }
    
    public function updatedServiceFilter()
    {
        $this->loadDashboardData();
    }
    
    public function render()
    {
        // Get client options for filter
        $clientOptions = Client::select('id', 'name')
            ->orderBy('name')
            ->get()
            ->map(function ($client) {
                return ['value' => $client->id, 'label' => $client->name];
            })
            ->toArray();
        
        // Prepend "All Clients" option
        array_unshift($clientOptions, ['value' => '', 'label' => 'All Clients']);
        
        // Get service options for filter
        $serviceOptions = Service::select('id', 'name')
            ->orderBy('name')
            ->get()
            ->map(function ($service) {
                return ['value' => $service->id, 'label' => $service->name];
            })
            ->toArray();
        
        // Prepend "All Services" option
        array_unshift($serviceOptions, ['value' => '', 'label' => 'All Services']);
        
        return view('livewire.dashboard', [
            'clientOptions' => $clientOptions,
            'serviceOptions' => $serviceOptions
        ]);
    }
}
