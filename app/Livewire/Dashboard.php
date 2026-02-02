<?php

namespace App\Livewire;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\RecurringTemplate;
use App\Models\RecurringInvoice;
use App\Models\Reimbursement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Computed;

class Dashboard extends Component
{
    // ============================================
    // TOP METRICS
    // ============================================

    #[Computed]
    public function totalRevenue()
    {
        return Payment::sum('amount');
    }

    #[Computed]
    public function revenueGrowth()
    {
        $currentMonth = Payment::whereMonth('payment_date', Carbon::now()->month)
            ->whereYear('payment_date', Carbon::now()->year)
            ->sum('amount');

        $lastMonth = Payment::whereMonth('payment_date', Carbon::now()->subMonth()->month)
            ->whereYear('payment_date', Carbon::now()->subMonth()->year)
            ->sum('amount');

        if ($lastMonth == 0)
            return 0;

        return round((($currentMonth - $lastMonth) / $lastMonth) * 100, 1);
    }

    #[Computed]
    public function outstandingAmount()
    {
        $totalOutstanding = Invoice::whereIn('status', ['sent', 'partially_paid', 'overdue'])
            ->sum('total_amount');

        $totalPaid = Payment::whereHas('invoice', function ($query) {
            $query->whereIn('status', ['sent', 'partially_paid', 'overdue']);
        })->sum('amount');

        return $totalOutstanding - $totalPaid;
    }

    #[Computed]
    public function overdueInvoices()
    {
        return Invoice::where('status', 'overdue')
            ->orWhere(function ($query) {
                $query->whereIn('status', ['sent', 'partially_paid'])
                    ->where('due_date', '<', Carbon::today());
            })
            ->count();
    }

    #[Computed]
    public function totalInvoices()
    {
        return Invoice::count();
    }

    #[Computed]
    public function invoiceStatusBreakdown()
    {
        return Invoice::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }

    #[Computed]
    public function collectionRate()
    {
        $totalInvoiced = Invoice::whereIn('status', ['sent', 'paid', 'partially_paid', 'overdue'])
            ->sum('total_amount');

        $totalCollected = Payment::sum('amount');

        if ($totalInvoiced == 0)
            return 0;

        return round(($totalCollected / $totalInvoiced) * 100, 1);
    }

    #[Computed]
    public function grossProfit()
    {
        $totalRevenue = Invoice::where('status', 'paid')->sum('total_amount');
        $totalCogs = InvoiceItem::whereHas('invoice', function ($query) {
            $query->where('status', 'paid');
        })->where('is_tax_deposit', false)->sum('cogs_amount');

        return $totalRevenue - $totalCogs;
    }

    #[Computed]
    public function profitMargin()
    {
        $revenue = Invoice::where('status', 'paid')->sum('total_amount');
        if ($revenue == 0)
            return 0;

        return round(($this->grossProfit / $revenue) * 100, 1);
    }

    #[Computed]
    public function activeClients()
    {
        return Client::where('status', 'active')->count();
    }

    #[Computed]
    public function newClientsThisMonth()
    {
        return Client::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
    }

    // ============================================
    // BANK & CASH FLOW
    // ============================================

    #[Computed]
    public function totalBankBalance()
    {
        return BankAccount::all()->sum(function ($account) {
            $credits = BankTransaction::where('bank_account_id', $account->id)
                ->where('transaction_type', 'credit')
                ->sum('amount');

            $debits = BankTransaction::where('bank_account_id', $account->id)
                ->where('transaction_type', 'debit')
                ->sum('amount');

            return $account->initial_balance + $credits - $debits;
        });
    }

    #[Computed]
    public function cashFlowThisMonth()
    {
        $credits = BankTransaction::where('transaction_type', 'credit')
            ->whereMonth('transaction_date', Carbon::now()->month)
            ->whereYear('transaction_date', Carbon::now()->year)
            ->sum('amount');

        $debits = BankTransaction::where('transaction_type', 'debit')
            ->whereMonth('transaction_date', Carbon::now()->month)
            ->whereYear('transaction_date', Carbon::now()->year)
            ->sum('amount');

        return $credits - $debits;
    }

    // ============================================
    // RECURRING REVENUE
    // ============================================

    #[Computed]
    public function monthlyRecurringRevenue()
    {
        return RecurringTemplate::where('status', 'active')
            ->where('frequency', 'monthly')
            ->get()
            ->sum(function ($template) {
                return $template->invoice_template['total_amount'] ?? 0;
            });
    }

    #[Computed]
    public function activeTemplates()
    {
        return RecurringTemplate::where('status', 'active')->count();
    }

    #[Computed]
    public function draftRecurringInvoices()
    {
        return RecurringInvoice::where('status', 'draft')->count();
    }

    // ============================================
    // REIMBURSEMENTS
    // ============================================

    #[Computed]
    public function pendingReimbursements()
    {
        return Reimbursement::where('status', 'pending')->count();
    }

    #[Computed]
    public function pendingReimbursementAmount()
    {
        return Reimbursement::where('status', 'pending')->sum('amount');
    }

    // ============================================
    // CHARTS DATA
    // ============================================

    #[Computed]
    public function monthlyRevenueChart()
    {
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $revenue = Payment::whereMonth('payment_date', $month->month)
                ->whereYear('payment_date', $month->year)
                ->sum('amount');

            $data[] = [
                'month' => $month->translatedFormat('M Y'),
                'revenue' => $revenue,
            ];
        }
        return $data;
    }

    #[Computed]
    public function invoiceStatusChart()
    {
        $statuses = [
            'draft' => __('pages.draft'),
            'sent' => __('pages.sent'),
            'paid' => __('pages.paid'),
            'partially_paid' => __('pages.installment'),
            'overdue' => __('pages.late')
        ];

        $data = Invoice::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) use ($statuses) {
                return [$statuses[$item->status] ?? $item->status => $item->count];
            })
            ->toArray();

        return $data;
    }

    #[Computed]
    public function profitRevenueChart()
    {
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);

            // Revenue
            $revenue = Payment::whereMonth('payment_date', $month->month)
                ->whereYear('payment_date', $month->year)
                ->sum('amount');

            // COGS for paid invoices in that month
            $invoiceIds = Invoice::where('status', 'paid')
                ->whereMonth('issue_date', $month->month)
                ->whereYear('issue_date', $month->year)
                ->pluck('id');

            $cogs = InvoiceItem::whereIn('invoice_id', $invoiceIds)
                ->where('is_tax_deposit', false)
                ->sum('cogs_amount');

            $data[] = [
                'month' => $month->translatedFormat('M'),
                'revenue' => $revenue,
                'profit' => max(0, $revenue - $cogs),
            ];
        }
        return $data;
    }

    // ============================================
    // TOP LISTS
    // ============================================

    #[Computed]
    public function topClients()
    {
        return Client::select('clients.*')
            ->join('invoice_items', 'clients.id', '=', 'invoice_items.client_id')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->where('invoices.status', 'paid')
            ->groupBy('clients.id', 'clients.name', 'clients.type', 'clients.email', 'clients.NPWP', 'clients.KPP', 'clients.logo', 'clients.status', 'clients.EFIN', 'clients.account_representative', 'clients.ar_phone_number', 'clients.person_in_charge', 'clients.address', 'clients.created_at', 'clients.updated_at')
            ->selectRaw('SUM(invoice_items.amount) as total_revenue')
            ->orderBy('total_revenue', 'desc')
            ->take(5)
            ->get()
            ->map(function ($client, $index) {
                return [
                    'rank' => $index + 1,
                    'name' => $client->name,
                    'type' => $client->type,
                    'total_revenue' => $client->total_revenue,
                ];
            });
    }

    #[Computed]
    public function recentInvoices()
    {
        return Invoice::with('client')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'client_name' => $invoice->client->name,
                    'total_amount' => $invoice->total_amount,
                    'status' => $invoice->status,
                    'issue_date' => $invoice->issue_date->format('d M Y'),
                ];
            });
    }

    #[Computed]
    public function topServices()
    {
        return InvoiceItem::select('service_name', DB::raw('SUM(amount) as total_revenue'))
            ->where('is_tax_deposit', false)
            ->groupBy('service_name')
            ->orderBy('total_revenue', 'desc')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->service_name,
                    'revenue' => $item->total_revenue,
                ];
            });
    }

    // ============================================
    // HELPER METHODS
    // ============================================

    public function formatCurrency($amount)
    {
        if ($amount >= 1000000000) {
            return 'Rp ' . number_format($amount / 1000000000, 1, ',', '.') . ' ' . __('pages.billion');
        } elseif ($amount >= 1000000) {
            return 'Rp ' . number_format($amount / 1000000, 1, ',', '.') . ' ' . __('pages.million');
        } else {
            return 'Rp ' . number_format($amount, 0, ',', '.');
        }
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}