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
        ['index' => 'description', 'label' => 'Transaction'],
        ['index' => 'bank_account_id', 'label' => 'Bank Account'],
        ['index' => 'category_id', 'label' => 'Category', 'sortable' => false],
        ['index' => 'transaction_date', 'label' => 'Date'],
        ['index' => 'amount', 'label' => 'Amount'],
        ['index' => 'action', 'sortable' => false],
    ];

    public function mount()
    {
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
            ->when(
                $this->search,
                fn($query) =>
                $query->whereHas(
                    'bankAccount',
                    fn($q) =>
                    $q->where('account_name', 'like', "%{$this->search}%")
                )->orWhere('description', 'like', "%{$this->search}%")
                    ->orWhere('reference_number', 'like', "%{$this->search}%")
            )
            ->when(
                $this->account_id || $this->constrainedBankAccountId,
                fn($query) =>
                $query->where('bank_account_id', $this->constrainedBankAccountId ?? $this->account_id)
            )
            ->when(
                $this->transaction_type,
                fn($query) =>
                $query->where('transaction_type', $this->transaction_type)
            )
            ->when(
                $this->category_id,
                fn($query) =>
                $query->where('category_id', $this->category_id)
            )
            // Date filtering - range overrides month
            ->when(
                $this->date_range,
                fn($query) =>
                $query->whereBetween('transaction_date', [
                    $this->date_range[0],
                    $this->date_range[1] ?? $this->date_range[0]
                ])
            )
            ->unless(
                $this->date_range,
                fn($query) =>
                $query->when(
                    $this->selected_month,
                    fn($q) => $q->whereYear('transaction_date', substr($this->selected_month, 0, 4))
                        ->whereMonth('transaction_date', substr($this->selected_month, 5, 2))
                )
            )
            ->when(
                $this->sort['column'] === 'bank_account_id',
                fn($query) =>
                $query->join('bank_accounts', 'bank_transactions.bank_account_id', '=', 'bank_accounts.id')
                    ->orderBy('bank_accounts.account_name', $this->sort['direction'])
                    ->select('bank_transactions.*')
                ,
                fn($query) =>
                $query->orderBy($this->sort['column'], $this->sort['direction'])
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
            ->prepend(['label' => 'Semua Kategori', 'value' => null])
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
            ->question("Hapus {$count} transaksi?", "Data transaksi yang dihapus tidak dapat dikembalikan.")
            ->confirm(method: 'bulkDelete')
            ->cancel()
            ->send();
    }

    public function bulkDelete(): void
    {
        if (empty($this->selected))
            return;

        $count = count($this->selected);

        foreach ($this->selected as $transactionId) {
            $transaction = BankTransaction::find($transactionId);
            if (!$transaction)
                continue;

            if ($transaction->reference_number && str_starts_with($transaction->reference_number, 'TRF')) {
                BankTransaction::where('reference_number', $transaction->reference_number)->delete();
            } else {
                $transaction->delete();
            }
        }

        $this->selected = [];
        $this->resetPage();
        $this->toast()->success("{$count} transaksi berhasil dihapus")->send();

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