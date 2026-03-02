<?php

namespace App\Livewire\CashFlow;

use App\Models\BankTransaction;
use App\Models\TransactionCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use TallStackUi\Traits\Interactions;

#[Lazy]
class Expenses extends Component
{
    use Interactions, WithPagination;

    // UI State
    public bool $guideModal = false;

    // Filters
    public $dateRange = [];
    public $categoryFilters = [];
    public $bankAccountFilters = [];
    public ?string $search = null;
    public ?int $quantity = 25;
    public array $selected = [];

    // Sorting
    public array $sort = ['column' => 'transaction_date', 'direction' => 'desc'];

    // Headers — populated in mount() so __() translation works
    public array $headers = [];

    public function mount(): void
    {
        $this->headers = [
            ['index' => 'transaction_date', 'label' => __('pages.col_date')],
            ['index' => 'category', 'label' => __('pages.col_category'), 'sortable' => false],
            ['index' => 'description', 'label' => __('pages.col_description')],
            ['index' => 'bank_account', 'label' => __('pages.col_bank'), 'sortable' => false],
            ['index' => 'amount', 'label' => __('pages.col_amount')],
            ['index' => 'action', 'label' => __('pages.col_action'), 'sortable' => false],
        ];
    }

    public function placeholder(): View
    {
        return view('livewire.placeholders.cashflow-skeleton');
    }

    #[On('transaction-created')]
    #[On('transaction-updated')]
    #[On('transaction-deleted')]
    #[On('transaction-categorized')]
    public function refreshData(): void
    {
        $this->reset('selected');
        $this->resetPage();
    }

    #[Computed]
    public function expenseCategories(): array
    {
        $categories = TransactionCategory::where('type', 'expense')
            ->with('parent')
            ->orderBy('label')
            ->get()
            ->map(fn($cat) => [
                'label' => $cat->full_path,
                'value' => $cat->id,
            ])
            ->toArray();

        array_unshift($categories, [
            'label' => __('pages.uncategorized_warning'),
            'value' => 'uncategorized'
        ]);

        return $categories;
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        return $this->getFilteredQuery()
            ->with(['bankAccount', 'category.parent'])
            ->orderBy('bank_transactions.' . $this->sort['column'], $this->sort['direction'])
            ->paginate($this->quantity)
            ->withQueryString();
    }

    #[Computed]
    public function totalExpense(): int
    {
        return (int) $this->getFilteredQuery()->sum('bank_transactions.amount');
    }

    public function export()
    {
        $data = $this->getFilteredQuery()
            ->with(['bankAccount', 'category.parent'])
            ->orderBy('bank_transactions.transaction_date', 'desc')
            ->get();

        if ($data->isEmpty()) {
            $this->toast()
                ->warning(__('common.warning'), __('pages.no_data_to_export'))
                ->send();
            return;
        }

        $filename = 'pengeluaran_' . now()->format('Y-m-d_His') . '.xlsx';

        $headings = [
            __('pages.excel_date'),
            __('pages.excel_category'),
            __('pages.excel_description'),
            __('pages.excel_bank'),
            __('pages.excel_reference'),
            __('pages.excel_amount'),
        ];
        $uncategorized = __('pages.uncategorized');

        return Excel::download(new class ($data, $headings, $uncategorized) implements
            \Maatwebsite\Excel\Concerns\FromCollection,
            \Maatwebsite\Excel\Concerns\WithHeadings,
            \Maatwebsite\Excel\Concerns\WithMapping {
            private $data;
            private array $headings;
            private string $uncategorized;

            public function __construct($data, array $headings, string $uncategorized)
            {
                $this->data = $data;
                $this->headings = $headings;
                $this->uncategorized = $uncategorized;
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
                    \Carbon\Carbon::parse($row->transaction_date)->format('d/m/Y'),
                    $row->category->full_path ?? $this->uncategorized,
                    $row->description,
                    $row->bankAccount->bank_name ?? '-',
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

        $data = BankTransaction::with(['bankAccount', 'category.parent'])
            ->whereIn('id', $this->selected)
            ->orderBy('transaction_date', 'desc')
            ->get();

        $filename = 'pengeluaran_selected_' . now()->format('Y-m-d_His') . '.xlsx';

        $this->toast()
            ->success(__('common.success'), __('pages.export_success', ['count' => count($this->selected)]))
            ->send();

        $headings = [
            __('pages.excel_date'),
            __('pages.excel_category'),
            __('pages.excel_description'),
            __('pages.excel_bank'),
            __('pages.excel_reference'),
            __('pages.excel_amount'),
        ];
        $uncategorized = __('pages.uncategorized');

        return Excel::download(new class ($data, $headings, $uncategorized) implements
            \Maatwebsite\Excel\Concerns\FromCollection,
            \Maatwebsite\Excel\Concerns\WithHeadings,
            \Maatwebsite\Excel\Concerns\WithMapping {
            private $data;
            private array $headings;
            private string $uncategorized;

            public function __construct($data, array $headings, string $uncategorized)
            {
                $this->data = $data;
                $this->headings = $headings;
                $this->uncategorized = $uncategorized;
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
                    \Carbon\Carbon::parse($row->transaction_date)->format('d/m/Y'),
                    $row->category->full_path ?? $this->uncategorized,
                    $row->description,
                    $row->bankAccount->bank_name ?? '-',
                    $row->reference_number ?? '-',
                    $row->amount
                ];
            }
        }, $filename);
    }

    public function openBulkCategorize(): void
    {
        if (empty($this->selected)) {
            $this->toast()
                ->warning(__('common.warning'), __('pages.select_transactions_to_cat'))
                ->send();
            return;
        }

        $this->dispatch('bulk-categorize', ids: $this->selected);
    }

    public function bulkDelete(): void
    {
        if (empty($this->selected)) {
            return;
        }

        $this->dialog()
            ->question(__('pages.bulk_delete_expenses', ['count' => count($this->selected)]), __('pages.bulk_delete_irreversible'))
            ->confirm(method: 'executeBulkDelete')
            ->cancel()
            ->send();
    }

    public function executeBulkDelete(): void
    {
        if (empty($this->selected)) {
            return;
        }

        $attachments = BankTransaction::whereIn('id', $this->selected)
            ->whereNotNull('attachment_path')
            ->pluck('attachment_path');

        foreach ($attachments as $path) {
            if (Storage::exists($path)) {
                Storage::delete($path);
            }
        }

        $count = BankTransaction::whereIn('id', $this->selected)->delete();

        $this->selected = [];
        $this->resetPage();

        $this->toast()
            ->success(__('common.success'), __('pages.bulk_delete_expenses_done', ['count' => $count]))
            ->send();
    }

    private function getFilteredQuery(): Builder
    {
        return BankTransaction::query()
            ->where('bank_transactions.transaction_type', 'debit')
            ->leftJoin('transaction_categories', 'bank_transactions.category_id', '=', 'transaction_categories.id')
            ->join('bank_accounts', 'bank_transactions.bank_account_id', '=', 'bank_accounts.id')
            ->where(function ($query) {
                $query->where('transaction_categories.type', 'expense')
                    ->orWhereNull('bank_transactions.category_id');
            })
            ->select('bank_transactions.*')
            ->when(
                $this->search,
                fn(Builder $q) =>
                $q->where(function ($query) {
                    $query->where('bank_transactions.description', 'like', "%{$this->search}%")
                        ->orWhere('bank_transactions.reference_number', 'like', "%{$this->search}%")
                        ->orWhere('bank_accounts.bank_name', 'like', "%{$this->search}%")
                        ->orWhere('bank_accounts.account_name', 'like', "%{$this->search}%");
                })
            )
            ->when(
                !empty($this->categoryFilters),
                fn(Builder $q) =>
                $q->where(function ($query) {
                    $hasUncategorized = in_array('uncategorized', $this->categoryFilters);
                    $categoryIds = array_filter($this->categoryFilters, fn($val) => $val !== 'uncategorized');

                    if ($hasUncategorized && !empty($categoryIds)) {
                        $query->whereNull('bank_transactions.category_id')
                            ->orWhereIn('bank_transactions.category_id', $categoryIds);
                    } elseif ($hasUncategorized) {
                        $query->whereNull('bank_transactions.category_id');
                    } else {
                        $query->whereIn('bank_transactions.category_id', $categoryIds);
                    }
                })
            )
            ->when(
                !empty($this->bankAccountFilters),
                fn(Builder $q) =>
                $q->whereIn('bank_transactions.bank_account_id', $this->bankAccountFilters)
            )
            ->when(
                !empty($this->dateRange) && count($this->dateRange) >= 2,
                fn(Builder $q) =>
                $q->whereBetween('bank_transactions.transaction_date', [$this->dateRange[0], $this->dateRange[1]])
            );
    }

    public function render()
    {
        return view('livewire.cash-flow.expenses');
    }
}
