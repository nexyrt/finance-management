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
        ['index' => 'action', 'label' => 'Aksi', 'sortable' => false],
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
        $categories = TransactionCategory::where('type', 'expense')
            ->orderBy('label')
            ->get()
            ->map(fn($cat) => [
                'label' => $cat->full_path,
                'value' => $cat->id,
            ])
            ->toArray();

        // Add uncategorized option at the beginning
        array_unshift($categories, [
            'label' => '❌ Belum Dikategorikan',
            'value' => 'uncategorized'
        ]);

        return $categories;
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        return BankTransaction::with(['bankAccount', 'category'])
            ->where('transaction_type', 'debit')
            ->where(function ($query) {
                $query->whereHas('category', fn($q) => $q->where('type', 'expense'))
                    ->orWhereNull('category_id');
            })
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
                                ->orWhere('account_name', 'like', "%{$this->search}%")
                        );
                })
            )
            ->when(
                !empty($this->categoryFilters),
                fn(Builder $q) =>
                $q->where(function ($query) {
                    $hasUncategorized = in_array('uncategorized', $this->categoryFilters);
                    $categoryIds = array_filter($this->categoryFilters, fn($val) => $val !== 'uncategorized');

                    if ($hasUncategorized && !empty($categoryIds)) {
                        $query->whereNull('category_id')
                            ->orWhereIn('category_id', $categoryIds);
                    } elseif ($hasUncategorized) {
                        $query->whereNull('category_id');
                    } else {
                        $query->whereIn('category_id', $categoryIds);
                    }
                })
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

    #[Computed]
    public function totalExpense(): int
    {
        return BankTransaction::where('transaction_type', 'debit')
            ->where(function ($query) {
                $query->whereHas('category', fn($q) => $q->where('type', 'expense'))
                    ->orWhereNull('category_id');
            })
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
                $q->where(function ($query) {
                    $hasUncategorized = in_array('uncategorized', $this->categoryFilters);
                    $categoryIds = array_filter($this->categoryFilters, fn($val) => $val !== 'uncategorized');

                    if ($hasUncategorized && !empty($categoryIds)) {
                        $query->whereNull('category_id')
                            ->orWhereIn('category_id', $categoryIds);
                    } elseif ($hasUncategorized) {
                        $query->whereNull('category_id');
                    } else {
                        $query->whereIn('category_id', $categoryIds);
                    }
                })
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
            ->sum('amount');
    }

    // Export functionality
    public function export()
    {
        $data = BankTransaction::with(['bankAccount', 'category'])
            ->where('transaction_type', 'debit')
            ->where(function ($query) {
                $query->whereHas('category', fn($q) => $q->where('type', 'expense'))
                    ->orWhereNull('category_id');
            })
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
                $q->where(function ($query) {
                    $hasUncategorized = in_array('uncategorized', $this->categoryFilters);
                    $categoryIds = array_filter($this->categoryFilters, fn($val) => $val !== 'uncategorized');

                    if ($hasUncategorized && !empty($categoryIds)) {
                        $query->whereNull('category_id')
                            ->orWhereIn('category_id', $categoryIds);
                    } elseif ($hasUncategorized) {
                        $query->whereNull('category_id');
                    } else {
                        $query->whereIn('category_id', $categoryIds);
                    }
                })
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
                    $row->category->full_path ?? 'Belum Dikategorikan',
                    $row->description,
                    $row->bankAccount->bank_name ?? '-',
                    $row->reference_number ?? '-',
                    $row->amount
                ];
            }
        }, $filename);
    }

    // Export with category breakdown
    public function exportWithCategoryBreakdown()
    {
        $data = BankTransaction::with(['bankAccount', 'category.parent'])
            ->where('transaction_type', 'debit')
            ->where(function ($query) {
                $query->whereHas('category', fn($q) => $q->where('type', 'expense'))
                    ->orWhereNull('category_id');
            })
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

        // Group by category
        $grouped = $data->groupBy(function ($item) {
            if (!$item->category)
                return 'Belum Dikategorikan';
            return $item->category->parent
                ? $item->category->parent->label
                : $item->category->label;
        });

        $filename = 'pengeluaran_breakdown_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new class ($grouped) implements
            \Maatwebsite\Excel\Concerns\WithMultipleSheets {

            private $grouped;

            public function __construct($grouped)
            {
                $this->grouped = $grouped;
            }

            public function sheets(): array
            {
                $sheets = [];

                // Summary sheet
                $sheets[] = new class ($this->grouped) implements
                    \Maatwebsite\Excel\Concerns\FromCollection,
                    \Maatwebsite\Excel\Concerns\WithHeadings,
                    \Maatwebsite\Excel\Concerns\WithTitle {

                    private $grouped;

                    public function __construct($grouped)
                    {
                        $this->grouped = $grouped;
                    }

                    public function collection()
                    {
                        return $this->grouped->map(function ($items, $category) {
                            return [
                                'category' => $category,
                                'count' => $items->count(),
                                'total' => $items->sum('amount')
                            ];
                        })->values();
                    }

                    public function headings(): array
                    {
                        return ['Kategori', 'Jumlah Transaksi', 'Total (Rp)'];
                    }

                    public function title(): string
                    {
                        return 'Summary';
                    }
                };

                // Detail sheets per category
                foreach ($this->grouped as $category => $items) {
                    $sheets[] = new class ($items, $category) implements
                        \Maatwebsite\Excel\Concerns\FromCollection,
                        \Maatwebsite\Excel\Concerns\WithHeadings,
                        \Maatwebsite\Excel\Concerns\WithMapping,
                        \Maatwebsite\Excel\Concerns\WithTitle {

                        private $items;
                        private $category;

                        public function __construct($items, $category)
                        {
                            $this->items = $items;
                            $this->category = $category;
                        }

                        public function collection()
                        {
                            return $this->items;
                        }

                        public function headings(): array
                        {
                            return ['Tanggal', 'Deskripsi', 'Bank', 'Jumlah'];
                        }

                        public function map($row): array
                        {
                            return [
                                \Carbon\Carbon::parse($row->transaction_date)->format('d/m/Y'),
                                $row->description,
                                $row->bankAccount->bank_name ?? '-',
                                $row->amount
                            ];
                        }

                        public function title(): string
                        {
                            return substr($this->category, 0, 31);
                        }
                    };
                }

                return $sheets;
            }
        }, $filename);
    }

    // Export comparison report
    public function exportComparison()
    {
        if (empty($this->dateRange) || count($this->dateRange) < 2) {
            $this->toast()
                ->warning('Perhatian', 'Pilih periode untuk perbandingan')
                ->send();
            return;
        }

        $currentStart = \Carbon\Carbon::parse($this->dateRange[0]);
        $currentEnd = \Carbon\Carbon::parse($this->dateRange[1]);
        $daysDiff = $currentStart->diffInDays($currentEnd) + 1;

        // Previous period (same duration)
        $previousStart = $currentStart->copy()->subDays($daysDiff);
        $previousEnd = $currentStart->copy()->subDay();

        // Current period data
        $currentData = BankTransaction::with('category.parent')
            ->where('transaction_type', 'debit')
            ->whereHas('category', fn($q) => $q->where('type', 'expense'))
            ->whereBetween('transaction_date', [$currentStart, $currentEnd])
            ->get()
            ->groupBy(function ($item) {
                if (!$item->category)
                    return 'Belum Dikategorikan';
                return $item->category->parent
                    ? $item->category->parent->label
                    : $item->category->label;
            });

        // Previous period data
        $previousData = BankTransaction::with('category.parent')
            ->where('transaction_type', 'debit')
            ->whereHas('category', fn($q) => $q->where('type', 'expense'))
            ->whereBetween('transaction_date', [$previousStart, $previousEnd])
            ->get()
            ->groupBy(function ($item) {
                if (!$item->category)
                    return 'Belum Dikategorikan';
                return $item->category->parent
                    ? $item->category->parent->label
                    : $item->category->label;
            });

        // Combine categories
        $allCategories = $currentData->keys()->merge($previousData->keys())->unique();

        $comparison = $allCategories->map(function ($category) use ($currentData, $previousData) {
            $currentTotal = $currentData->get($category)?->sum('amount') ?? 0;
            $previousTotal = $previousData->get($category)?->sum('amount') ?? 0;
            $difference = $currentTotal - $previousTotal;
            $percentChange = $previousTotal > 0 ? (($difference / $previousTotal) * 100) : 0;

            return [
                'category' => $category,
                'current' => $currentTotal,
                'previous' => $previousTotal,
                'difference' => $difference,
                'percent' => round($percentChange, 2)
            ];
        });

        $filename = 'pengeluaran_comparison_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new class ($comparison, $currentStart, $currentEnd, $previousStart, $previousEnd) implements
            \Maatwebsite\Excel\Concerns\FromCollection,
            \Maatwebsite\Excel\Concerns\WithHeadings,
            \Maatwebsite\Excel\Concerns\WithMapping,
            \Maatwebsite\Excel\Concerns\WithTitle {

            private $comparison;
            private $currentStart;
            private $currentEnd;
            private $previousStart;
            private $previousEnd;

            public function __construct($comparison, $currentStart, $currentEnd, $previousStart, $previousEnd)
            {
                $this->comparison = $comparison;
                $this->currentStart = $currentStart;
                $this->currentEnd = $currentEnd;
                $this->previousStart = $previousStart;
                $this->previousEnd = $previousEnd;
            }

            public function collection()
            {
                return $this->comparison;
            }

            public function headings(): array
            {
                return [
                    'Kategori',
                    "Periode Saat Ini\n({$this->currentStart->format('d/m/Y')} - {$this->currentEnd->format('d/m/Y')})",
                    "Periode Sebelumnya\n({$this->previousStart->format('d/m/Y')} - {$this->previousEnd->format('d/m/Y')})",
                    'Selisih (Rp)',
                    'Perubahan (%)'
                ];
            }

            public function map($row): array
            {
                return [
                    $row['category'],
                    $row['current'],
                    $row['previous'],
                    $row['difference'],
                    $row['percent'] . '%'
                ];
            }

            public function title(): string
            {
                return 'Comparison Report';
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
        $count = count($this->selected);

        BankTransaction::whereIn('id', $this->selected)->delete();

        $this->selected = [];
        $this->resetPage();

        $this->toast()
            ->success('Berhasil', $count . ' pengeluaran telah dihapus')
            ->send();
    }

    public function render()
    {
        return view('livewire.cash-flow.expenses-tab');
    }
}