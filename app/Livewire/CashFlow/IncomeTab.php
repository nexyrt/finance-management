<?php

namespace App\Livewire\CashFlow;

use App\Models\BankTransaction;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\TransactionCategory;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use TallStackUi\Traits\Interactions;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class IncomeTab extends Component
{
    use WithPagination, Interactions;

    // Table properties
    public array $selected = [];
    public array $sort = ['column' => 'date', 'direction' => 'desc'];
    public ?int $quantity = 25;

    // Filters
    public ?string $categoryFilter = null;
    public ?string $sourceFilter = null; // bank_transaction or payment
    public array $dateRange = [];
    public string $search = '';

    public array $headers = [
        ['index' => 'date', 'label' => 'Date'],
        ['index' => 'description', 'label' => 'Description'],
        ['index' => 'source_type', 'label' => 'Source', 'sortable' => false],
        ['index' => 'bank_account', 'label' => 'Account', 'sortable' => false],
        ['index' => 'category', 'label' => 'Category', 'sortable' => false],
        ['index' => 'amount', 'label' => 'Amount', 'sortable' => false],
    ];

    public function mount()
    {
        $this->dateRange = [];
    }

    #[Computed]
    public function incomeTransactions(): LengthAwarePaginator
    {
        // Create base queries using Query Builder directly
        $bankIncomeQuery = DB::table('bank_transactions')
            ->join('bank_accounts', 'bank_transactions.bank_account_id', '=', 'bank_accounts.id')
            ->leftJoin('transaction_categories', 'bank_transactions.category_id', '=', 'transaction_categories.id')
            ->where('bank_transactions.transaction_type', 'credit')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('transaction_categories as tc')
                    ->whereRaw('tc.id = bank_transactions.category_id')
                    ->where('tc.type', 'income');
            })
            ->select([
                'bank_transactions.id',
                'bank_transactions.transaction_date as date',
                'bank_transactions.description',
                'bank_transactions.reference_number',
                'bank_transactions.amount',
                'bank_accounts.account_name',
                'transaction_categories.label as category_label',
                DB::raw("'Bank Income' as source_type"),
                DB::raw("'bank_transaction' as source"),
                'bank_transactions.category_id'
            ]);

        $paymentQuery = DB::table('payments')
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->join('bank_accounts', 'payments.bank_account_id', '=', 'bank_accounts.id')
            ->join('clients', 'invoices.billed_to_id', '=', 'clients.id')
            ->select([
                'payments.id',
                'payments.payment_date as date',
                DB::raw("CONCAT('Payment from ', clients.name, ' - Invoice #', invoices.invoice_number) as description"),
                'payments.reference_number',
                'payments.amount',
                'bank_accounts.account_name',
                DB::raw("NULL as category_label"),
                DB::raw("'Invoice Payment' as source_type"),
                DB::raw("'payment' as source"),
                DB::raw("NULL as category_id")
            ]);

        // Apply filters to both queries
        $this->applyFiltersToQueryBuilder($bankIncomeQuery, $paymentQuery);

        // Union the queries
        $unionQuery = $bankIncomeQuery->union($paymentQuery);

        // Apply sorting
        match ($this->sort['column']) {
            'date' => $unionQuery->orderBy('date', $this->sort['direction']),
            'description' => $unionQuery->orderBy('description', $this->sort['direction']),
            default => $unionQuery->orderBy('date', 'desc')
        };

        return $unionQuery->paginate($this->quantity)->withQueryString();
    }

    private function applyFiltersToQueryBuilder($bankQuery, $paymentQuery): void
    {
        // Date range filter
        if (!empty($this->dateRange) && count($this->dateRange) >= 2) {
            $bankQuery->whereBetween('bank_transactions.transaction_date', $this->dateRange);
            $paymentQuery->whereBetween('payments.payment_date', $this->dateRange);
        }

        // Search filter
        if ($this->search) {
            $bankQuery->where(function ($q) {
                $q->where('bank_transactions.description', 'like', '%' . $this->search . '%')
                    ->orWhere('bank_transactions.reference_number', 'like', '%' . $this->search . '%')
                    ->orWhere('bank_accounts.account_name', 'like', '%' . $this->search . '%');
            });

            $paymentQuery->where(function ($q) {
                $q->where('payments.reference_number', 'like', '%' . $this->search . '%')
                    ->orWhere('invoices.invoice_number', 'like', '%' . $this->search . '%')
                    ->orWhere('clients.name', 'like', '%' . $this->search . '%')
                    ->orWhere('bank_accounts.account_name', 'like', '%' . $this->search . '%');
            });
        }

        // Category filter (only applies to bank transactions)
        if ($this->categoryFilter) {
            $bankQuery->where('bank_transactions.category_id', $this->categoryFilter);
        }

        // Source filter
        if ($this->sourceFilter === 'bank_transaction') {
            // We'll need to handle this differently since we can't modify union after creation
        } elseif ($this->sourceFilter === 'payment') {
            // We'll need to handle this differently since we can't modify union after creation
        }
    }



    #[Computed]
    public function stats(): array
    {
        // Use same filtering logic for stats
        $dateFilter = !empty($this->dateRange) ? $this->dateRange : null;

        // Bank Income
        $bankIncome = BankTransaction::where('transaction_type', 'credit')
            ->whereHas('category', fn($q) => $q->where('type', 'income'))
            ->when($dateFilter, fn($q) => $q->whereBetween('transaction_date', $dateFilter))
            ->when($this->categoryFilter, fn($q) => $q->where('category_id', $this->categoryFilter))
            ->sum('amount');

        // Payment Income
        $paymentIncome = Payment::when($dateFilter, fn($q) => $q->whereBetween('payment_date', $dateFilter))
            ->sum('amount');

        // Calculate profit
        $totalCogs = InvoiceItem::whereHas('invoice', function ($query) use ($dateFilter) {
            $query->when($dateFilter, fn($q) => $q->whereBetween('issue_date', $dateFilter));
        })->where('is_tax_deposit', false)->sum('cogs_amount');

        $totalTaxDeposits = InvoiceItem::whereHas('invoice', function ($query) use ($dateFilter) {
            $query->when($dateFilter, fn($q) => $q->whereBetween('issue_date', $dateFilter));
        })->where('is_tax_deposit', true)->sum('amount');

        $netPaymentProfit = $paymentIncome - $totalCogs - $totalTaxDeposits;
        $totalIncome = $bankIncome + $netPaymentProfit;

        return [
            'bank_income' => $bankIncome,
            'payment_income' => $paymentIncome,
            'net_payment_profit' => $netPaymentProfit,
            'total_income' => $totalIncome,
            'total_transactions' => $this->incomeTransactions->total(),
        ];
    }

    #[Computed]
    public function categories(): array
    {
        return TransactionCategory::where('type', 'income')
            ->orderBy('label')
            ->get()
            ->map(fn($cat) => ['label' => $cat->full_path, 'value' => $cat->id])
            ->toArray();
    }

    // Filter watchers following Invoice pattern
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatedSourceFilter()
    {
        $this->resetPage();
    }

    public function updatedDateRange()
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->fill([
            'categoryFilter' => null,
            'sourceFilter' => null,
            'dateRange' => [],
            'search' => ''
        ]);
        $this->resetPage();
    }

    // Export methods following Invoice pattern
    public function exportExcel()
    {
        if (empty($this->selected)) {
            $this->toast()->warning('Warning', 'Select transactions to export')->send();
            return;
        }

        $count = count($this->selected);
        $this->toast()->success('Export Started', "Exporting {$count} transactions")->send();
    }

    public function exportAll()
    {
        $this->toast()->success('Export Started', 'Exporting all income transactions')->send();
    }

    public function bulkDelete(): void
    {
        if (empty($this->selected)) {
            $this->toast()->warning('Warning', 'Select transactions to delete')->send();
            return;
        }

        try {
            DB::transaction(function () {
                // Delete selected bank transactions
                BankTransaction::whereIn('id', $this->selected)
                    ->where('transaction_type', 'credit')
                    ->whereHas('category', fn($q) => $q->where('type', 'income'))
                    ->delete();

                // Note: We don't delete payments as they are tied to invoices
                // This bulk delete only applies to bank transactions
            });

            $deletedCount = count($this->selected);
            $this->selected = [];
            $this->resetPage();
            $this->toast()->success('Success', "Successfully deleted {$deletedCount} bank transactions")->send();
        } catch (\Exception $e) {
            $this->toast()->error('Error', 'Failed to delete: ' . $e->getMessage())->send();
        }
    }

    public function render()
    {
        return view('livewire.cash-flow.income-tab');
    }
}