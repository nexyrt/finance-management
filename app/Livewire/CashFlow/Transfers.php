<?php

namespace App\Livewire\CashFlow;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\TransactionCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use TallStackUi\Traits\Interactions;

class Transfers extends Component
{
    use Interactions, WithPagination;

    // Section toggle: 'transfers' or 'adjustments'
    public string $section = 'transfers';

    // ==========================================
    // TRANSFER PROPERTIES
    // ==========================================
    public $dateRange = [];
    public $bankAccountFilters = [];
    public ?string $search = null;
    public ?int $quantity = 25;
    public array $selected = [];
    public array $sort = ['column' => 'transaction_date', 'direction' => 'desc'];

    // Headers — populated in mount() so __() translation works
    public array $headers = [];

    // ==========================================
    // ADJUSTMENT PROPERTIES
    // ==========================================
    public $adjDateRange = [];
    public $adjCategoryFilters = [];
    public $adjBankAccountFilters = [];
    public ?string $adjSearch = null;
    public array $adjSelected = [];
    public array $adjSort = ['column' => 'transaction_date', 'direction' => 'desc'];

    public array $adjHeaders = [];

    public function mount(): void
    {
        $this->headers = [
            ['index' => 'transaction_date', 'label' => __('pages.col_date')],
            ['index' => 'from_account', 'label' => __('pages.col_from'), 'sortable' => false],
            ['index' => 'to_account', 'label' => __('pages.col_to'), 'sortable' => false],
            ['index' => 'description', 'label' => __('pages.col_description')],
            ['index' => 'amount', 'label' => __('pages.col_transfer_amount')],
            ['index' => 'total_debit', 'label' => __('pages.col_total_debit'), 'sortable' => false],
            ['index' => 'action', 'label' => __('pages.col_action'), 'sortable' => false],
        ];

        $this->adjHeaders = [
            ['index' => 'transaction_date', 'label' => __('pages.col_date')],
            ['index' => 'transaction_type', 'label' => __('pages.col_type'), 'sortable' => false],
            ['index' => 'description', 'label' => __('pages.col_description')],
            ['index' => 'category', 'label' => __('pages.col_category'), 'sortable' => false],
            ['index' => 'bank_account', 'label' => __('pages.col_bank'), 'sortable' => false],
            ['index' => 'amount', 'label' => __('pages.col_amount')],
            ['index' => 'action', 'label' => __('pages.col_action'), 'sortable' => false],
        ];
    }

    // ==========================================
    // EVENT LISTENERS
    // ==========================================
    #[On('transfer-completed')]
    #[On('transaction-created')]
    #[On('transaction-deleted')]
    public function refreshData(): void
    {
        $this->reset('selected', 'adjSelected');
        $this->resetPage();
        $this->resetPage('adjPage');
    }

    // ==========================================
    // SECTION TOGGLE
    // ==========================================
    public function switchSection(string $section): void
    {
        $this->section = $section;
    }

    // ==========================================
    // SHARED COMPUTED
    // ==========================================
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

    // ==========================================
    // TRANSFER COMPUTED & METHODS
    // ==========================================
    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        $transfers = BankTransaction::with(['bankAccount', 'category'])
            ->whereHas('category', fn($q) => $q->where('type', 'transfer'))
            ->where('transaction_type', 'credit')
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
                !empty($this->bankAccountFilters),
                fn(Builder $q) =>
                $q->where(function ($query) {
                    $query->whereIn('bank_account_id', $this->bankAccountFilters)
                        ->orWhereHas(
                            'pairedTransaction',
                            fn($pair) =>
                            $pair->whereIn('bank_account_id', $this->bankAccountFilters)
                        );
                })
            )
            ->when(
                !empty($this->dateRange) && count($this->dateRange) >= 2,
                fn(Builder $q) =>
                $q->whereBetween('transaction_date', [$this->dateRange[0], $this->dateRange[1]])
            )
            ->orderBy(...array_values($this->sort))
            ->paginate($this->quantity)
            ->withQueryString();

        $transfers->getCollection()->transform(function ($credit) {
            $debit = BankTransaction::where('reference_number', $credit->reference_number)
                ->where('transaction_type', 'debit')
                ->with('bankAccount')
                ->first();

            $credit->from_account = $debit?->bankAccount;
            $credit->total_debit = $debit?->amount ?? 0;

            return $credit;
        });

        return $transfers;
    }

    #[Computed]
    public function totalTransfers(): int
    {
        return BankTransaction::whereHas('category', fn($q) => $q->where('type', 'transfer'))
            ->where('transaction_type', 'credit')
            ->when(
                $this->search,
                fn(Builder $q) =>
                $q->where(function ($query) {
                    $query->where('description', 'like', "%{$this->search}%")
                        ->orWhere('reference_number', 'like', "%{$this->search}%");
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

    #[Computed]
    public function totalAdminFees(): int
    {
        $creditIds = BankTransaction::whereHas('category', fn($q) => $q->where('type', 'transfer'))
            ->where('transaction_type', 'credit')
            ->when(
                $this->search,
                fn(Builder $q) =>
                $q->where(function ($query) {
                    $query->where('description', 'like', "%{$this->search}%")
                        ->orWhere('reference_number', 'like', "%{$this->search}%");
                })
            )
            ->when(
                !empty($this->dateRange) && count($this->dateRange) >= 2,
                fn(Builder $q) =>
                $q->whereBetween('transaction_date', [$this->dateRange[0], $this->dateRange[1]])
            )
            ->pluck('reference_number');

        $debits = BankTransaction::whereIn('reference_number', $creditIds)
            ->where('transaction_type', 'debit')
            ->get();

        return $debits->sum(function ($debit) {
            $credit = BankTransaction::where('reference_number', $debit->reference_number)
                ->where('transaction_type', 'credit')
                ->first();

            return $debit->amount - ($credit?->amount ?? 0);
        });
    }

    public function export()
    {
        $data = $this->getExportData();

        if ($data->isEmpty()) {
            $this->toast()->warning(__('common.warning'), __('pages.no_data_to_export'))->send();
            return;
        }

        $filename = 'transfer_' . now()->format('Y-m-d_His') . '.xlsx';

        $headings = [
            __('pages.excel_date'),
            __('pages.excel_from_bank'),
            __('pages.excel_to_bank'),
            __('pages.excel_description'),
            __('pages.excel_transfer_amount'),
            __('pages.excel_admin_fee'),
            __('pages.excel_total_debit'),
            __('pages.excel_reference'),
        ];

        return Excel::download(new class ($data, $headings) implements
            \Maatwebsite\Excel\Concerns\FromCollection,
            \Maatwebsite\Excel\Concerns\WithHeadings,
            \Maatwebsite\Excel\Concerns\WithMapping {
            private $data;
            private array $headings;

            public function __construct($data, array $headings) { $this->data = $data; $this->headings = $headings; }
            public function collection() { return $this->data; }

            public function headings(): array
            {
                return $this->headings;
            }

            public function map($row): array
            {
                return [
                    \Carbon\Carbon::parse($row->transaction_date)->format('d/m/Y'),
                    $row->from_account?->bank_name ?? '-',
                    $row->bankAccount->bank_name,
                    $row->description,
                    $row->amount,
                    $row->total_debit - $row->amount,
                    $row->total_debit,
                    $row->reference_number ?? '-',
                ];
            }
        }, $filename);
    }

    public function exportSelected()
    {
        if (empty($this->selected)) {
            $this->toast()->warning(__('common.warning'), __('pages.select_data_to_export'))->send();
            return;
        }

        $data = $this->getExportData($this->selected);
        $filename = 'transfer_selected_' . now()->format('Y-m-d_His') . '.xlsx';

        $this->toast()->success(__('common.success'), __('pages.export_success', ['count' => count($this->selected)]))->send();

        $headings = [
            __('pages.excel_date'),
            __('pages.excel_from_bank'),
            __('pages.excel_to_bank'),
            __('pages.excel_description'),
            __('pages.excel_transfer_amount'),
            __('pages.excel_admin_fee'),
            __('pages.excel_total_debit'),
            __('pages.excel_reference'),
        ];

        return Excel::download(new class ($data, $headings) implements
            \Maatwebsite\Excel\Concerns\FromCollection,
            \Maatwebsite\Excel\Concerns\WithHeadings,
            \Maatwebsite\Excel\Concerns\WithMapping {
            private $data;
            private array $headings;

            public function __construct($data, array $headings) { $this->data = $data; $this->headings = $headings; }
            public function collection() { return $this->data; }

            public function headings(): array
            {
                return $this->headings;
            }

            public function map($row): array
            {
                return [
                    \Carbon\Carbon::parse($row->transaction_date)->format('d/m/Y'),
                    $row->from_account?->bank_name ?? '-',
                    $row->bankAccount->bank_name,
                    $row->description,
                    $row->amount,
                    $row->total_debit - $row->amount,
                    $row->total_debit,
                    $row->reference_number ?? '-',
                ];
            }
        }, $filename);
    }

    private function getExportData($ids = null)
    {
        $query = BankTransaction::with(['bankAccount', 'category'])
            ->whereHas('category', fn($q) => $q->where('type', 'transfer'))
            ->where('transaction_type', 'credit');

        if ($ids) {
            $query->whereIn('id', $ids);
        } else {
            $query->when(
                $this->search,
                fn(Builder $q) =>
                $q->where(function ($query) {
                    $query->where('description', 'like', "%{$this->search}%")
                        ->orWhere('reference_number', 'like', "%{$this->search}%");
                })
            )->when(
                !empty($this->dateRange) && count($this->dateRange) >= 2,
                fn(Builder $q) =>
                $q->whereBetween('transaction_date', [$this->dateRange[0], $this->dateRange[1]])
            );
        }

        $transfers = $query->orderBy('transaction_date', 'desc')->get();

        $transfers->transform(function ($credit) {
            $debit = BankTransaction::where('reference_number', $credit->reference_number)
                ->where('transaction_type', 'debit')
                ->with('bankAccount')
                ->first();

            $credit->from_account = $debit?->bankAccount;
            $credit->total_debit = $debit?->amount ?? 0;

            return $credit;
        });

        return $transfers;
    }

    public function bulkDelete()
    {
        if (empty($this->selected)) {
            return;
        }

        $this->dialog()
            ->question(__('pages.bulk_delete_transfers', ['count' => count($this->selected)]), __('pages.transfer_pair_deleted'))
            ->confirm(method: 'executeBulkDelete')
            ->cancel()
            ->send();
    }

    public function executeBulkDelete()
    {
        $count = count($this->selected);

        $credits = BankTransaction::whereIn('id', $this->selected)->get();

        foreach ($credits as $credit) {
            BankTransaction::where('reference_number', $credit->reference_number)
                ->where('transaction_type', 'debit')
                ->delete();
            $credit->delete();
        }

        $this->selected = [];
        $this->resetPage();

        $this->toast()->success(__('common.success'), __('pages.bulk_delete_transfers_done', ['count' => $count]))->send();
    }

    // ==========================================
    // ADJUSTMENT COMPUTED & METHODS
    // ==========================================
    #[Computed]
    public function adjustmentCategories(): array
    {
        return TransactionCategory::where('type', 'adjustment')
            ->orderBy('label')
            ->get()
            ->map(fn($cat) => ['label' => $cat->label, 'value' => $cat->id])
            ->toArray();
    }

    #[Computed]
    public function adjRows(): LengthAwarePaginator
    {
        return BankTransaction::with(['bankAccount', 'category'])
            ->whereHas('category', fn($q) => $q->where('type', 'adjustment'))
            ->when(
                $this->adjSearch,
                fn(Builder $q) =>
                $q->where(function ($query) {
                    $query->where('description', 'like', "%{$this->adjSearch}%")
                        ->orWhere('reference_number', 'like', "%{$this->adjSearch}%");
                })
            )
            ->when(
                !empty($this->adjCategoryFilters),
                fn(Builder $q) => $q->whereIn('category_id', $this->adjCategoryFilters)
            )
            ->when(
                !empty($this->adjBankAccountFilters),
                fn(Builder $q) => $q->whereIn('bank_account_id', $this->adjBankAccountFilters)
            )
            ->when(
                !empty($this->adjDateRange) && count($this->adjDateRange) >= 2,
                fn(Builder $q) =>
                $q->whereBetween('transaction_date', [$this->adjDateRange[0], $this->adjDateRange[1]])
            )
            ->orderBy(...array_values($this->adjSort))
            ->paginate($this->quantity, ['*'], 'adjPage')
            ->withQueryString();
    }

    #[Computed]
    public function adjStats(): array
    {
        $query = BankTransaction::whereHas('category', fn($q) => $q->where('type', 'adjustment'))
            ->when($this->adjSearch, fn(Builder $q) => $q->where(function ($query) {
                $query->where('description', 'like', "%{$this->adjSearch}%")
                    ->orWhere('reference_number', 'like', "%{$this->adjSearch}%");
            }))
            ->when(!empty($this->adjCategoryFilters), fn(Builder $q) => $q->whereIn('category_id', $this->adjCategoryFilters))
            ->when(!empty($this->adjBankAccountFilters), fn(Builder $q) => $q->whereIn('bank_account_id', $this->adjBankAccountFilters))
            ->when(!empty($this->adjDateRange) && count($this->adjDateRange) >= 2, fn(Builder $q) =>
                $q->whereBetween('transaction_date', [$this->adjDateRange[0], $this->adjDateRange[1]]));

        $totalDebits = (clone $query)->where('transaction_type', 'debit')->sum('amount');
        $totalCredits = (clone $query)->where('transaction_type', 'credit')->sum('amount');

        return [
            'total_debits' => $totalDebits,
            'total_credits' => $totalCredits,
            'net_adjustment' => $totalCredits - $totalDebits,
        ];
    }

    public function adjBulkDelete(): void
    {
        if (empty($this->adjSelected)) {
            return;
        }

        $this->dialog()
            ->question(__('pages.bulk_delete_adjustments', ['count' => count($this->adjSelected)]), __('pages.bulk_delete_irreversible'))
            ->confirm(method: 'executeAdjBulkDelete')
            ->cancel()
            ->send();
    }

    public function executeAdjBulkDelete(): void
    {
        $count = count($this->adjSelected);
        BankTransaction::whereIn('id', $this->adjSelected)->delete();

        $this->adjSelected = [];
        $this->resetPage('adjPage');

        $this->toast()->success(__('common.success'), __('pages.bulk_delete_adj_done', ['count' => $count]))->send();
    }

    // ==========================================
    // FILTER WATCHERS
    // ==========================================
    public function updatedDateRange() { $this->resetPage(); }
    public function updatedBankAccountFilters() { $this->resetPage(); }
    public function updatedSearch() { $this->resetPage(); }
    public function updatedAdjDateRange() { $this->resetPage('adjPage'); }
    public function updatedAdjCategoryFilters() { $this->resetPage('adjPage'); }
    public function updatedAdjBankAccountFilters() { $this->resetPage('adjPage'); }
    public function updatedAdjSearch() { $this->resetPage('adjPage'); }

    public function render()
    {
        return view('livewire.cash-flow.transfers');
    }
}
