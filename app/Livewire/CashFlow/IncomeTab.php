<?php

namespace App\Livewire\CashFlow;

use App\Models\BankTransaction;
use App\Models\Client;
use App\Models\Payment;
use App\Models\TransactionCategory;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use TallStackUi\Traits\Interactions;

class IncomeTab extends Component
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

    public function mount()
    {
        $this->dateRange = [];
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
    public function incomeData(): LengthAwarePaginator
    {
        $payments = Payment::query()
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->join('clients', 'invoices.billed_to_id', '=', 'clients.id')
            ->join('bank_accounts', 'payments.bank_account_id', '=', 'bank_accounts.id')
            ->select([
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

    // Get all filtered data (for export)
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

    // Export to Excel
    public function export()
    {
        $data = $this->getFilteredData();

        if ($data->isEmpty()) {
            $this->toast()
                ->warning('Perhatian', 'Tidak ada data untuk diekspor')
                ->send();
            return;
        }

        $filename = 'pemasukan_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new class ($data) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithMapping {
            private $data;

            public function __construct($data)
            {
                $this->data = $data;
            }

            public function collection()
            {
                return $this->data;
            }

            public function headings(): array
            {
                return [
                    'Tanggal',
                    'Sumber',
                    'Invoice',
                    'Klien',
                    'Deskripsi',
                    'Kategori',
                    'Bank',
                    'Referensi',
                    'Jumlah'
                ];
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

    // Export selected items
    public function exportSelected()
    {
        if (empty($this->selected)) {
            $this->toast()
                ->warning('Perhatian', 'Pilih data yang ingin diekspor')
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
            ->success('Berhasil', count($this->selected) . ' item berhasil diekspor')
            ->send();

        return Excel::download(new class ($data) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithMapping {
            private $data;

            public function __construct($data)
            {
                $this->data = $data;
            }

            public function collection()
            {
                return $this->data;
            }

            public function headings(): array
            {
                return [
                    'Tanggal',
                    'Sumber',
                    'Invoice',
                    'Klien',
                    'Deskripsi',
                    'Kategori',
                    'Bank',
                    'Referensi',
                    'Jumlah'
                ];
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

    // Filter watchers
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

    // Sorting
    public function sortBy($column)
    {
        if ($this->sort['column'] === $column) {
            $this->sort['direction'] = $this->sort['direction'] === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sort['column'] = $column;
            $this->sort['direction'] = 'asc';
        }

        $this->resetPage();
    }

    // Actions
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

    // Bulk delete
    public function bulkDelete()
    {
        if (empty($this->selected)) {
            return;
        }

        $this->dialog()
            ->question('Hapus ' . count($this->selected) . ' item?', 'Data yang dihapus tidak dapat dikembalikan.')
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
            ->success('Berhasil', $deleted . ' item telah dihapus')
            ->send();
    }

    public function render()
    {
        return view('livewire.cash-flow.income-tab');
    }
}