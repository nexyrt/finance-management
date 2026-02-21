<?php

namespace App\Livewire\CashFlow;

use App\Models\BankTransaction;
use App\Models\Client;
use App\Models\Payment;
use App\Models\TransactionCategory;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use TallStackUi\Traits\Interactions;

class Income extends Component
{
    use Interactions, WithPagination;

    // Filters
    public $dateRange = [];
    public $categoryFilters = [];
    public $clientFilters = [];
    public ?string $search = null;
    public ?int $quantity = 25;
    public array $selected = [];

    // Sorting
    public array $sort = ['column' => 'date', 'direction' => 'desc'];

    // Headers — populated in mount() so __() translation works
    public array $headers = [];

    public function mount(): void
    {
        $this->headers = [
            ['index' => 'date', 'label' => __('pages.col_date')],
            ['index' => 'source_type', 'label' => __('pages.col_source'), 'sortable' => false],
            ['index' => 'client_description', 'label' => __('pages.col_client_desc'), 'sortable' => false],
            ['index' => 'category_label', 'label' => __('pages.col_category'), 'sortable' => false],
            ['index' => 'amount', 'label' => __('pages.col_amount')],
            ['index' => 'action', 'label' => __('pages.col_action'), 'sortable' => false],
        ];
    }

    #[On('transaction-created')]
    #[On('payment-updated')]
    #[On('payment-deleted')]
    #[On('transaction-deleted')]
    public function refreshData(): void
    {
        $this->reset('selected');
        $this->resetPage();
    }

    #[Computed]
    public function clients()
    {
        return Client::orderBy('name')
            ->get()
            ->map(fn($client) => [
                'label' => $client->name,
                'value' => $client->id,
            ])
            ->toArray();
    }

    #[Computed]
    public function incomeCategories()
    {
        return TransactionCategory::where('type', 'income')
            ->orderBy('label')
            ->get()
            ->map(fn($cat) => [
                'label' => $cat->full_path,
                'value' => $cat->id,
            ])
            ->toArray();
    }

    #[Computed]
    public function totalIncome(): int
    {
        $payments = Payment::query()
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->join('clients', 'invoices.billed_to_id', '=', 'clients.id')
            ->join('bank_accounts', 'payments.bank_account_id', '=', 'bank_accounts.id');

        if (!empty($this->clientFilters)) {
            $payments->whereIn('clients.id', $this->clientFilters);
        }

        if (!empty($this->dateRange) && count($this->dateRange) >= 2 && $this->dateRange[0] && $this->dateRange[1]) {
            $payments->whereBetween('payments.payment_date', [$this->dateRange[0], $this->dateRange[1]]);
        }

        if ($this->search) {
            $payments->where(function ($q) {
                $q->where('invoices.invoice_number', 'like', '%' . $this->search . '%')
                    ->orWhere('clients.name', 'like', '%' . $this->search . '%')
                    ->orWhere('payments.reference_number', 'like', '%' . $this->search . '%')
                    ->orWhere('bank_accounts.bank_name', 'like', '%' . $this->search . '%');
            });
        }

        $paymentTotal = $payments->sum('payments.amount');

        $transactions = BankTransaction::query()
            ->join('transaction_categories', 'bank_transactions.category_id', '=', 'transaction_categories.id')
            ->where('bank_transactions.transaction_type', 'credit')
            ->where('transaction_categories.type', 'income');

        if (!empty($this->categoryFilters)) {
            $transactions->whereIn('bank_transactions.category_id', $this->categoryFilters);
        }

        if (!empty($this->dateRange) && count($this->dateRange) >= 2 && $this->dateRange[0] && $this->dateRange[1]) {
            $transactions->whereBetween('bank_transactions.transaction_date', [$this->dateRange[0], $this->dateRange[1]]);
        }

        if ($this->search) {
            $transactions->where(function ($q) {
                $q->where('bank_transactions.description', 'like', '%' . $this->search . '%')
                    ->orWhere('bank_transactions.reference_number', 'like', '%' . $this->search . '%');
            });
        }

        $transactionTotal = $transactions->sum('bank_transactions.amount');

        return $paymentTotal + $transactionTotal;
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        $payments = Payment::query()
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->join('clients', 'invoices.billed_to_id', '=', 'clients.id')
            ->join('bank_accounts', 'payments.bank_account_id', '=', 'bank_accounts.id')
            ->select([
                DB::raw("CONCAT('payment-', payments.id) as uid"),
                'payments.id',
                'payments.payment_date as date',
                'payments.amount',
                'payments.reference_number',
                'payments.attachment_path',
                'payments.attachment_name',
                'invoices.invoice_number',
                'clients.name as client_name',
                'bank_accounts.bank_name',
                DB::raw("'payment' as source_type"),
                DB::raw('NULL as category_id'),
                DB::raw('NULL as category_label'),
                DB::raw('NULL as description'),
            ]);

        if (!empty($this->clientFilters)) {
            $payments->whereIn('clients.id', $this->clientFilters);
        }

        if (!empty($this->dateRange) && count($this->dateRange) >= 2 && $this->dateRange[0] && $this->dateRange[1]) {
            $payments->whereBetween('payments.payment_date', [$this->dateRange[0], $this->dateRange[1]]);
        }

        if ($this->search) {
            $payments->where(function ($q) {
                $q->where('invoices.invoice_number', 'like', '%' . $this->search . '%')
                    ->orWhere('clients.name', 'like', '%' . $this->search . '%')
                    ->orWhere('payments.reference_number', 'like', '%' . $this->search . '%')
                    ->orWhere('bank_accounts.bank_name', 'like', '%' . $this->search . '%');
            });
        }

        $transactions = BankTransaction::query()
            ->join('bank_accounts', 'bank_transactions.bank_account_id', '=', 'bank_accounts.id')
            ->join('transaction_categories', 'bank_transactions.category_id', '=', 'transaction_categories.id')
            ->where('bank_transactions.transaction_type', 'credit')
            ->where('transaction_categories.type', 'income')
            ->select([
                DB::raw("CONCAT('transaction-', bank_transactions.id) as uid"),
                'bank_transactions.id',
                'bank_transactions.transaction_date as date',
                'bank_transactions.amount',
                'bank_transactions.reference_number',
                'bank_transactions.attachment_path',
                'bank_transactions.attachment_name',
                DB::raw('NULL as invoice_number'),
                DB::raw('NULL as client_name'),
                'bank_accounts.bank_name',
                DB::raw("'transaction' as source_type"),
                'transaction_categories.id as category_id',
                'transaction_categories.label as category_label',
                'bank_transactions.description',
            ]);

        if (!empty($this->categoryFilters)) {
            $transactions->whereIn('bank_transactions.category_id', $this->categoryFilters);
        }

        if (!empty($this->dateRange) && count($this->dateRange) >= 2 && $this->dateRange[0] && $this->dateRange[1]) {
            $transactions->whereBetween('bank_transactions.transaction_date', [$this->dateRange[0], $this->dateRange[1]]);
        }

        if ($this->search) {
            $transactions->where(function ($q) {
                $q->where('bank_transactions.description', 'like', '%' . $this->search . '%')
                    ->orWhere('bank_transactions.reference_number', 'like', '%' . $this->search . '%')
                    ->orWhere('bank_accounts.bank_name', 'like', '%' . $this->search . '%');
            });
        }

        $query = $payments->union($transactions);
        $results = $query->get();

        // Sorting
        $sortColumn = $this->sort['column'];
        $results = $this->sort['direction'] === 'desc'
            ? $results->sortByDesc($sortColumn)
            : $results->sortBy($sortColumn);

        // Pagination
        $total = $results->count();
        $currentPage = $this->getPage();
        $offset = ($currentPage - 1) * $this->quantity;
        $items = $results->slice($offset, $this->quantity)->values();

        return new LengthAwarePaginator(
            $items,
            $total,
            $this->quantity,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    private function getFilteredData()
    {
        $payments = Payment::query()
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->join('clients', 'invoices.billed_to_id', '=', 'clients.id')
            ->join('bank_accounts', 'payments.bank_account_id', '=', 'bank_accounts.id')
            ->select([
                'payments.payment_date as date',
                'payments.amount',
                'payments.reference_number',
                'invoices.invoice_number',
                'clients.name as client_name',
                'bank_accounts.bank_name',
                DB::raw("'Payment' as source_type"),
                DB::raw('NULL as category_label'),
                DB::raw('NULL as description'),
            ]);

        if (!empty($this->clientFilters)) {
            $payments->whereIn('clients.id', $this->clientFilters);
        }

        if (!empty($this->dateRange) && count($this->dateRange) >= 2) {
            $payments->whereBetween('payments.payment_date', [$this->dateRange[0], $this->dateRange[1]]);
        }

        if ($this->search) {
            $payments->where(function ($q) {
                $q->where('invoices.invoice_number', 'like', '%' . $this->search . '%')
                    ->orWhere('clients.name', 'like', '%' . $this->search . '%');
            });
        }

        $transactions = BankTransaction::query()
            ->join('bank_accounts', 'bank_transactions.bank_account_id', '=', 'bank_accounts.id')
            ->join('transaction_categories', 'bank_transactions.category_id', '=', 'transaction_categories.id')
            ->where('bank_transactions.transaction_type', 'credit')
            ->where('transaction_categories.type', 'income')
            ->select([
                'bank_transactions.transaction_date as date',
                'bank_transactions.amount',
                'bank_transactions.reference_number',
                DB::raw('NULL as invoice_number'),
                DB::raw('NULL as client_name'),
                'bank_accounts.bank_name',
                DB::raw("'Direct Income' as source_type"),
                'transaction_categories.label as category_label',
                'bank_transactions.description',
            ]);

        if (!empty($this->categoryFilters)) {
            $transactions->whereIn('bank_transactions.category_id', $this->categoryFilters);
        }

        if (!empty($this->dateRange) && count($this->dateRange) >= 2) {
            $transactions->whereBetween('bank_transactions.transaction_date', [$this->dateRange[0], $this->dateRange[1]]);
        }

        if ($this->search) {
            $transactions->where(function ($q) {
                $q->where('bank_transactions.description', 'like', '%' . $this->search . '%');
            });
        }

        return $payments->union($transactions)->get()->sortByDesc('date');
    }

    public function export()
    {
        $data = $this->getFilteredData();

        if ($data->isEmpty()) {
            $this->toast()
                ->warning(__('common.warning'), __('pages.no_data_to_export'))
                ->send();
            return;
        }

        $filename = 'pemasukan_' . now()->format('Y-m-d_His') . '.xlsx';

        $headings = [
            __('pages.excel_date'),
            __('pages.excel_source'),
            __('pages.excel_invoice'),
            __('pages.excel_client'),
            __('pages.excel_description'),
            __('pages.excel_category'),
            __('pages.excel_bank'),
            __('pages.excel_reference'),
            __('pages.excel_amount'),
        ];

        return Excel::download(new class ($data, $headings) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithMapping {
            private $data;
            private array $headings;

            public function __construct($data, array $headings)
            {
                $this->data = $data;
                $this->headings = $headings;
            }

            public function collection()
            {
                return $this->data;
            }

            public function headings(): array
            {
                return $this->headings;
            }

            public function map($row): array
            {
                return [
                    \Carbon\Carbon::parse($row->date)->format('d/m/Y'),
                    $row->source_type,
                    $row->invoice_number ?? '-',
                    $row->client_name ?? '-',
                    $row->description ?? '-',
                    $row->category_label ?? '-',
                    $row->bank_name,
                    $row->reference_number ?? '-',
                    $row->amount
                ];
            }
        }, $filename);
    }

    public function exportPdf()
    {
        $startDate = !empty($this->dateRange) && isset($this->dateRange[0]) ? $this->dateRange[0] : null;
        $endDate = !empty($this->dateRange) && isset($this->dateRange[1]) ? $this->dateRange[1] : null;

        $url = route('cash-flow.export.pdf', array_filter([
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]));

        return redirect($url);
    }

    public function exportSelected()
    {
        if (empty($this->selected)) {
            $this->toast()
                ->warning(__('common.warning'), __('pages.select_data_to_export'))
                ->send();
            return;
        }

        $ids = collect($this->selected)->map(function ($item) {
            [$type, $id] = explode('-', $item);
            return ['type' => $type, 'id' => $id];
        });

        $payments = Payment::query()
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->join('clients', 'invoices.billed_to_id', '=', 'clients.id')
            ->join('bank_accounts', 'payments.bank_account_id', '=', 'bank_accounts.id')
            ->whereIn('payments.id', $ids->where('type', 'payment')->pluck('id'))
            ->select([
                'payments.payment_date as date',
                'payments.amount',
                'payments.reference_number',
                'invoices.invoice_number',
                'clients.name as client_name',
                'bank_accounts.bank_name',
                DB::raw("'Payment' as source_type"),
                DB::raw('NULL as category_label'),
                DB::raw('NULL as description'),
            ]);

        $transactions = BankTransaction::query()
            ->join('bank_accounts', 'bank_transactions.bank_account_id', '=', 'bank_accounts.id')
            ->join('transaction_categories', 'bank_transactions.category_id', '=', 'transaction_categories.id')
            ->whereIn('bank_transactions.id', $ids->where('type', 'transaction')->pluck('id'))
            ->select([
                'bank_transactions.transaction_date as date',
                'bank_transactions.amount',
                'bank_transactions.reference_number',
                DB::raw('NULL as invoice_number'),
                DB::raw('NULL as client_name'),
                'bank_accounts.bank_name',
                DB::raw("'Direct Income' as source_type"),
                'transaction_categories.label as category_label',
                'bank_transactions.description',
            ]);

        $data = $payments->union($transactions)->get()->sortByDesc('date');

        $filename = 'pemasukan_selected_' . now()->format('Y-m-d_His') . '.xlsx';

        $this->toast()
            ->success(__('common.success'), __('pages.export_success', ['count' => count($this->selected)]))
            ->send();

        $headings = [
            __('pages.excel_date'),
            __('pages.excel_source'),
            __('pages.excel_invoice'),
            __('pages.excel_client'),
            __('pages.excel_description'),
            __('pages.excel_category'),
            __('pages.excel_bank'),
            __('pages.excel_reference'),
            __('pages.excel_amount'),
        ];

        return Excel::download(new class ($data, $headings) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithMapping {
            private $data;
            private array $headings;

            public function __construct($data, array $headings)
            {
                $this->data = $data;
                $this->headings = $headings;
            }

            public function collection()
            {
                return $this->data;
            }

            public function headings(): array
            {
                return $this->headings;
            }

            public function map($row): array
            {
                return [
                    \Carbon\Carbon::parse($row->date)->format('d/m/Y'),
                    $row->source_type,
                    $row->invoice_number ?? '-',
                    $row->client_name ?? '-',
                    $row->description ?? '-',
                    $row->category_label ?? '-',
                    $row->bank_name,
                    $row->reference_number ?? '-',
                    $row->amount
                ];
            }
        }, $filename);
    }

    public function updatedDateRange()
    {
        $this->resetPage();
    }

    public function updatedCategoryFilters()
    {
        $this->resetPage();
    }

    public function updatedClientFilters()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function viewAttachment($sourceType, $id)
    {
        $this->dispatch('view-attachment', sourceType: $sourceType, id: $id);
    }

    public function editPayment($id)
    {
        $this->dispatch('edit-payment', paymentId: $id);
    }

    public function deleteItem($sourceType, $id)
    {
        if ($sourceType === 'payment') {
            $this->dispatch('delete-payment', paymentId: $id);
        } else {
            $this->dispatch('delete-transaction', transactionId: $id);
        }
    }

    public function bulkDelete()
    {
        if (empty($this->selected)) {
            return;
        }

        $this->dialog()
            ->question(__('pages.bulk_delete_income', ['count' => count($this->selected)]), __('pages.bulk_delete_irreversible'))
            ->confirm(method: 'executeBulkDelete')
            ->cancel()
            ->send();
    }

    public function executeBulkDelete()
    {
        $deleted = 0;

        foreach ($this->selected as $item) {
            [$type, $id] = explode('-', $item);

            if ($type === 'payment') {
                Payment::find($id)?->delete();
                $deleted++;
            } elseif ($type === 'transaction') {
                BankTransaction::find($id)?->delete();
                $deleted++;
            }
        }

        $this->selected = [];
        $this->resetPage();

        $this->toast()
            ->success(__('common.success'), __('pages.bulk_delete_income_done', ['count' => $deleted]))
            ->send();
    }

    public function render()
    {
        return view('livewire.cash-flow.income');
    }
}
