<?php

namespace App\Livewire\Accounts;

use App\Models\BankTransaction;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class TransactionList extends Component
{
    use Interactions, WithPagination;

    public ?int $selectedAccountId = null;

    // Table properties
    public ?string $search = null;

    public ?int $quantity = 15;

    public array $sort = ['column' => 'transaction_date', 'direction' => 'desc'];

    public array $selected = [];

    // Filters
    public ?string $transaction_type = null;

    public ?int $category_id = null;

    public ?string $selected_month = null;

    public array $headers = [];

    public function mount(?int $selectedAccountId = null): void
    {
        $this->selectedAccountId = $selectedAccountId;

        $this->headers = [
            ['index' => 'description', 'label' => __('pages.trx_col_transaction')],
            ['index' => 'category_id', 'label' => __('pages.trx_col_category'), 'sortable' => false],
            ['index' => 'transaction_date', 'label' => __('pages.trx_col_date')],
            ['index' => 'amount', 'label' => __('pages.trx_col_amount')],
            ['index' => 'action', 'label' => '', 'sortable' => false],
        ];
    }

    #[On('account-selected')]
    public function handleAccountChange(int $accountId): void
    {
        $this->selectedAccountId = $accountId;
        $this->reset(['search', 'transaction_type', 'category_id', 'selected_month', 'selected']);
        $this->resetPage();
    }

    #[Computed]
    public function transactions(): LengthAwarePaginator
    {
        if (! $this->selectedAccountId) {
            return new LengthAwarePaginator([], 0, $this->quantity);
        }

        return BankTransaction::with(['category.parent'])
            ->where('bank_account_id', $this->selectedAccountId)
            ->when($this->search, fn ($q) => $q->where(function ($query) {
                $query->where('description', 'like', "%{$this->search}%")
                    ->orWhere('reference_number', 'like', "%{$this->search}%");
            }))
            ->when($this->transaction_type, fn ($q) => $q->where('transaction_type', $this->transaction_type))
            ->when($this->category_id, fn ($q) => $q->where('category_id', $this->category_id))
            ->when($this->selected_month, fn ($q) => $q
                ->whereYear('transaction_date', substr($this->selected_month, 0, 4))
                ->whereMonth('transaction_date', substr($this->selected_month, 5, 2))
            )
            ->orderBy($this->sort['column'], $this->sort['direction'])
            ->paginate($this->quantity);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedTransactionType(): void
    {
        $this->resetPage();
    }

    public function updatedCategoryId(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedMonth(): void
    {
        $this->resetPage();
    }

    public function deleteTransaction(int $transactionId): void
    {
        $this->dispatch('delete-transaction', transactionId: $transactionId);
    }

    #[Renderless]
    public function confirmBulkDelete(): void
    {
        if (empty($this->selected)) {
            return;
        }

        $count = count($this->selected);
        $this->dialog()
            ->question(__('pages.bulk_delete_confirm_title', ['count' => $count]), __('pages.bulk_delete_confirm_message'))
            ->confirm(method: 'bulkDelete')
            ->cancel()
            ->send();
    }

    public function bulkDelete(): void
    {
        if (empty($this->selected)) {
            return;
        }

        $count = count($this->selected);

        // Batch load all selected transactions
        $transactions = BankTransaction::whereIn('id', $this->selected)->get();

        // Collect transfer reference numbers for paired deletion
        $transferRefs = $transactions
            ->filter(fn ($t) => $t->reference_number && str_starts_with($t->reference_number, 'TRF'))
            ->pluck('reference_number')
            ->unique()
            ->values()
            ->all();

        // Delete transfer pairs in one query
        if (! empty($transferRefs)) {
            BankTransaction::whereIn('reference_number', $transferRefs)->delete();
        }

        // Delete remaining non-transfer transactions
        $nonTransferIds = $transactions
            ->filter(fn ($t) => ! $t->reference_number || ! str_starts_with($t->reference_number, 'TRF'))
            ->pluck('id')
            ->all();

        if (! empty($nonTransferIds)) {
            BankTransaction::whereIn('id', $nonTransferIds)->delete();
        }

        $this->selected = [];
        $this->resetPage();
        $this->toast()->success(__('pages.bulk_delete_success', ['count' => $count]))->send();

        $this->dispatch('transaction-deleted');
    }

    public function render()
    {
        return view('livewire.accounts.transaction-list');
    }
}
