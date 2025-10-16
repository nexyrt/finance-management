<?php

namespace App\Livewire\CashFlow;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\TransactionCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use TallStackUi\Traits\Interactions;

class ExpensesTab extends Component
{
    use Interactions, WithPagination;

    // Filters
    public $dateRange = [];
    public $categoryFilters = [];
    public $bankAccountFilters = [];
    public ?string $search = null;
    public ?int $quantity = 25;
    public array $selected = [];

    // Sorting
    public array $sort = ['column' => 'transaction_date', 'direction' => 'desc'];

    // Headers
    public array $headers = [
        ['index' => 'transaction_date', 'label' => 'Tanggal'],
        ['index' => 'category', 'label' => 'Kategori', 'sortable' => false],
        ['index' => 'description', 'label' => 'Deskripsi'],
        ['index' => 'bank_account', 'label' => 'Bank', 'sortable' => false],
        ['index' => 'amount', 'label' => 'Jumlah'],
        ['index' => 'action', 'sortable' => false],
    ];

    #[Computed]
    public function bankAccounts()
    {
        return BankAccount::orderBy('bank_name')
            ->get()
            ->map(fn($account) => [
                'label' => $account->bank_name . ' - ' . $account->account_name,
                'value' => $account->id,
            ])
            ->toArray();
    }

    #[Computed]
    public function expenseCategories()
    {
        return TransactionCategory::where('type', 'expense')
            ->orderBy('label')
            ->get()
            ->map(fn($cat) => [
                'label' => $cat->full_path,
                'value' => $cat->id,
            ])
            ->toArray();
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        return BankTransaction::with(['bankAccount', 'category'])
            ->where('transaction_type', 'debit')
            ->whereHas('category', fn($q) => $q->where('type', 'expense'))
            ->when(
                $this->search,
                fn(Builder $q) =>
                $q->where(function ($query) {
                    $query->where('description', 'like', "%{$this->search}%")
                        ->orWhere('reference_number', 'like', "%{$this->search}%")
                        ->orWhereHas(
                            'bankAccount',
                            fn($bank) =>
                            $bank->where('bank_name', 'like', "%{$this->search}%")
                        );
                })
            )
            ->when(
                !empty($this->categoryFilters),
                fn(Builder $q) =>
                $q->whereIn('category_id', $this->categoryFilters)
            )
            ->when(
                !empty($this->bankAccountFilters),
                fn(Builder $q) =>
                $q->whereIn('bank_account_id', $this->bankAccountFilters)
            )
            ->when(
                !empty($this->dateRange) && count($this->dateRange) >= 2,
                fn(Builder $q) =>
                $q->whereBetween('transaction_date', [$this->dateRange[0], $this->dateRange[1]])
            )
            ->orderBy(...array_values($this->sort))
            ->paginate($this->quantity)
            ->withQueryString();
    }

    // Export functionality
    public function export()
    {
        $data = BankTransaction::with(['bankAccount', 'category'])
            ->where('transaction_type', 'debit')
            ->whereHas('category', fn($q) => $q->where('type', 'expense'))
            ->when(
                $this->search,
                fn(Builder $q) =>
                $q->where(function ($query) {
                    $query->where('description', 'like', "%{$this->search}%")
                        ->orWhere('reference_number', 'like', "%{$this->search}%");
                })
            )
            ->when(
                !empty($this->categoryFilters),
                fn(Builder $q) =>
                $q->whereIn('category_id', $this->categoryFilters)
            )
            ->when(
                !empty($this->bankAccountFilters),
                fn(Builder $q) =>
                $q->whereIn('bank_account_id', $this->bankAccountFilters)
            )
            ->when(
                !empty($this->dateRange) && count($this->dateRange) >= 2,
                fn(Builder $q) =>
                $q->whereBetween('transaction_date', [$this->dateRange[0], $this->dateRange[1]])
            )
            ->orderBy('transaction_date', 'desc')
            ->get();

        if ($data->isEmpty()) {
            $this->toast()
                ->warning('Perhatian', 'Tidak ada data untuk diekspor')
                ->send();
            return;
        }

        $filename = 'pengeluaran_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new class ($data) implements
            \Maatwebsite\Excel\Concerns\FromCollection,
            \Maatwebsite\Excel\Concerns\WithHeadings,
            \Maatwebsite\Excel\Concerns\WithMapping {
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
                return ['Tanggal', 'Kategori', 'Deskripsi', 'Bank', 'Referensi', 'Jumlah'];
            }

            public function map($row): array
            {
                return [
                    \Carbon\Carbon::parse($row->transaction_date)->format('d/m/Y'),
                    $row->category->full_path ?? '-',
                    $row->description,
                    $row->bankAccount->bank_name ?? '-',
                    $row->reference_number ?? '-',
                    $row->amount
                ];
            }
        }, $filename);
    }

    public function exportSelected()
    {
        if (empty($this->selected)) {
            $this->toast()
                ->warning('Perhatian', 'Pilih data yang ingin diekspor')
                ->send();
            return;
        }

        $data = BankTransaction::with(['bankAccount', 'category'])
            ->whereIn('id', $this->selected)
            ->orderBy('transaction_date', 'desc')
            ->get();

        $filename = 'pengeluaran_selected_' . now()->format('Y-m-d_His') . '.xlsx';

        $this->toast()
            ->success('Berhasil', count($this->selected) . ' item berhasil diekspor')
            ->send();

        return Excel::download(new class ($data) implements
            \Maatwebsite\Excel\Concerns\FromCollection,
            \Maatwebsite\Excel\Concerns\WithHeadings,
            \Maatwebsite\Excel\Concerns\WithMapping {
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
                return ['Tanggal', 'Kategori', 'Deskripsi', 'Bank', 'Referensi', 'Jumlah'];
            }

            public function map($row): array
            {
                return [
                    \Carbon\Carbon::parse($row->transaction_date)->format('d/m/Y'),
                    $row->category->full_path ?? '-',
                    $row->description,
                    $row->bankAccount->bank_name ?? '-',
                    $row->reference_number ?? '-',
                    $row->amount
                ];
            }
        }, $filename);
    }

    // Bulk delete
    public function bulkDelete()
    {
        if (empty($this->selected)) {
            return;
        }

        $this->dialog()
            ->question('Hapus ' . count($this->selected) . ' pengeluaran?', 'Data yang dihapus tidak dapat dikembalikan.')
            ->confirm(method: 'executeBulkDelete')
            ->cancel()
            ->send();
    }

    public function executeBulkDelete()
    {
        BankTransaction::whereIn('id', $this->selected)->delete();

        $count = count($this->selected);
        $this->selected = [];
        $this->resetPage();

        $this->toast()
            ->success('Berhasil', $count . ' pengeluaran telah dihapus')
            ->send();
    }

    public function createExpense()
    {
        $this->dispatch('create-transaction', allowedTypes: ['debit']);
    }

    public function render()
    {
        return view('livewire.cash-flow.expenses-tab');
    }
}