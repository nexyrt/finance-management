<?php

namespace App\Livewire\Transactions;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\TransactionCategory;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Renderless;
use Livewire\Attributes\Reactive;
use TallStackUi\Traits\Interactions;

class Listing extends Component
{
    use WithPagination, Interactions;

    #[Reactive]
    public ?int $constrainedBankAccountId = null;

    // Table properties
    public ?string $search = null;
    public ?int $quantity = 25;
    public array $sort = ['column' => 'transaction_date', 'direction' => 'desc'];
    public array $selected = [];

    // Filters
    public ?string $account_id = null;
    public ?string $transaction_type = null;
    public ?int $category_id = null;
    public ?string $selected_month = null;
    public ?array $date_range = null;

    // Attachment modal
    public bool $attachmentModal = false;
    public ?BankTransaction $selectedTransaction = null;

    public array $headers = [
        ['index' => 'description', 'label' => '#'],
        ['index' => 'bank_account_id', 'label' => '#'],
        ['index' => 'category_id', 'label' => '#', 'sortable' => false],
        ['index' => 'transaction_date', 'label' => '#'],
        ['index' => 'amount', 'label' => '#'],
        ['index' => 'action', 'sortable' => false],
    ];

    public function mount()
    {
        // Translate headers
        $this->headers = [
            ['index' => 'description', 'label' => __('pages.trx_col_transaction')],
            ['index' => 'bank_account_id', 'label' => __('pages.trx_col_bank_account')],
            ['index' => 'category_id', 'label' => __('pages.trx_col_category'), 'sortable' => false],
            ['index' => 'transaction_date', 'label' => __('pages.trx_col_date')],
            ['index' => 'amount', 'label' => __('pages.trx_col_amount')],
            ['index' => 'action', 'sortable' => false],
        ];

        // Auto-set account filter if constrained
        if ($this->constrainedBankAccountId) {
            $this->account_id = (string) $this->constrainedBankAccountId;
        }

        $this->dispatchFilterChange();
    }

    #[Computed]
    public function transactions()
    {
        return BankTransaction::with(['bankAccount', 'category.parent'])
            ->join('bank_accounts', 'bank_transactions.bank_account_id', '=', 'bank_accounts.id')
            ->select('bank_transactions.*')
            ->when(
                $this->search,
                fn($query) =>
                $query->where(function ($q) {
                    $q->where('bank_transactions.description', 'like', "%{$this->search}%")
                        ->orWhere('bank_transactions.reference_number', 'like', "%{$this->search}%")
                        ->orWhere('bank_accounts.account_name', 'like', "%{$this->search}%");
                })
            )
            ->when(
                $this->account_id || $this->constrainedBankAccountId,
                fn($query) =>
                $query->where('bank_transactions.bank_account_id', $this->constrainedBankAccountId ?? $this->account_id)
            )
            ->when(
                $this->transaction_type,
                fn($query) =>
                $query->where('bank_transactions.transaction_type', $this->transaction_type)
            )
            ->when(
                $this->category_id,
                fn($query) =>
                $query->where('bank_transactions.category_id', $this->category_id)
            )
            // Date filtering - range overrides month
            ->when(
                $this->date_range,
                fn($query) =>
                $query->whereBetween('bank_transactions.transaction_date', [
                    $this->date_range[0],
                    $this->date_range[1] ?? $this->date_range[0]
                ])
            )
            ->unless(
                $this->date_range,
                fn($query) =>
                $query->when(
                    $this->selected_month,
                    fn($q) => $q->whereYear('bank_transactions.transaction_date', substr($this->selected_month, 0, 4))
                        ->whereMonth('bank_transactions.transaction_date', substr($this->selected_month, 5, 2))
                )
            )
            ->when(
                $this->sort['column'] === 'bank_account_id',
                fn($query) =>
                $query->orderBy('bank_accounts.account_name', $this->sort['direction']),
                fn($query) =>
                $query->orderBy('bank_transactions.' . $this->sort['column'], $this->sort['direction'])
            )
            ->paginate($this->quantity)
            ->withQueryString();
    }

    #[Computed]
    public function accounts()
    {
        return BankAccount::orderBy('account_name')->get();
    }

    #[Computed]
    public function categories()
    {
        return TransactionCategory::with('parent')
            ->orderBy('type')
            ->orderBy('label')
            ->get()
            ->map(fn($cat) => [
                'label' => $cat->parent ? $cat->parent->label . ' → ' . $cat->label : $cat->label,
                'value' => $cat->id,
            ])
            ->prepend(['label' => __('pages.all_categories_option'), 'value' => null])
            ->toArray();
    }

    // Dispatch filter changes to parent components
    protected function dispatchFilterChange(): void
    {
        $this->dispatch('filter-changed', [
            'account_id' => $this->account_id,
            'transaction_type' => $this->transaction_type,
            'category_id' => $this->category_id,
            'search' => $this->search,
            'selected_month' => $this->selected_month,
            'date_range' => $this->date_range,
        ]);
    }

    // Watch for filter changes
    public function updatedAccountId(): void
    {
        $this->resetPage();
        $this->dispatchFilterChange();
    }

    public function updatedTransactionType(): void
    {
        $this->resetPage();
        $this->dispatchFilterChange();
    }

    public function updatedCategoryId(): void
    {
        $this->resetPage();
        $this->dispatchFilterChange();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->dispatchFilterChange();
    }

    public function updatedSelectedMonth(): void
    {
        $this->resetPage();
        $this->dispatchFilterChange();
    }

    public function updatedDateRange(): void
    {
        $this->resetPage();
        $this->dispatchFilterChange();
    }

    public function viewAttachment(int $id): void
    {
        $this->selectedTransaction = BankTransaction::find($id);
        $this->attachmentModal = true;
    }

    #[Renderless]
    public function confirmBulkDelete(): void
    {
        if (empty($this->selected))
            return;

        $count = count($this->selected);
        $this->dialog()
            ->question(__('pages.bulk_delete_confirm_title', ['count' => $count]), __('pages.bulk_delete_confirm_message'))
            ->confirm(method: 'bulkDelete')
            ->cancel()
            ->send();
    }

    public function bulkDelete(): void
    {
        if (empty($this->selected))
            return;

        $count = count($this->selected);

        // Batch load all selected transactions instead of N+1
        $transactions = BankTransaction::whereIn('id', $this->selected)->get();

        // Collect transfer reference numbers that need paired deletion
        $transferRefs = $transactions
            ->filter(fn($t) => $t->reference_number && str_starts_with($t->reference_number, 'TRF'))
            ->pluck('reference_number')
            ->unique()
            ->values()
            ->all();

        // Delete transfer pairs in one query
        if (!empty($transferRefs)) {
            BankTransaction::whereIn('reference_number', $transferRefs)->delete();
        }

        // Delete remaining non-transfer transactions
        $nonTransferIds = $transactions
            ->filter(fn($t) => !$t->reference_number || !str_starts_with($t->reference_number, 'TRF'))
            ->pluck('id')
            ->all();

        if (!empty($nonTransferIds)) {
            BankTransaction::whereIn('id', $nonTransferIds)->delete();
        }

        $this->selected = [];
        $this->resetPage();
        $this->toast()->success(__('pages.bulk_delete_success', ['count' => $count]))->send();

        // Refresh parent stats
        $this->dispatch('transaction-deleted');
        $this->dispatchFilterChange();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'account_id', 'transaction_type', 'category_id', 'selected_month', 'date_range', 'selected']);
        $this->resetPage();
        $this->dispatchFilterChange();
    }

    public function deleteTransaction(int $transactionId): void
    {
        $this->dispatch('delete-transaction', transactionId: $transactionId);
    }

    public function render()
    {
        return view('livewire.transactions.listing');
    }
}