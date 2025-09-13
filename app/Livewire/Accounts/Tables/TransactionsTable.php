<?php

namespace App\Livewire\Accounts\Tables;

use App\Models\BankTransaction;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use TallStackUi\Traits\Interactions;

class TransactionsTable extends Component
{
    use WithPagination, Interactions;

    public $selectedAccountId;

    // Internal filters
    public $search = '';
    public $transactionType = '';
    public $dateRange = [];

    // Table state
    public array $sort = ['column' => 'transaction_date', 'direction' => 'desc'];
    public array $selected = [];
    public ?int $quantity = 10;

    // Headers
    public array $headers = [
        ['index' => 'description', 'label' => 'Description'],
        ['index' => 'reference_number', 'label' => 'Reference'],
        ['index' => 'transaction_date', 'label' => 'Date'],
        ['index' => 'amount', 'label' => 'Amount'],
        ['index' => 'action', 'label' => 'Action', 'sortable' => false],
    ];

    public function render()
    {
        return view('livewire.accounts.tables.transactions-table');
    }

    #[On('account-selected')]
    public function handleAccountChange($accountId): void
    {
        $this->selectedAccountId = $accountId;
        $this->clearFilters();
        $this->resetPage();
    }

    #[Computed]
    public function rows()
    {
        if (!$this->selectedAccountId)
            return collect();

        return BankTransaction::where('bank_account_id', $this->selectedAccountId)
            ->when($this->search, fn($q) => $q->where(function ($query) {
                $query->where('description', 'like', "%{$this->search}%")
                    ->orWhere('reference_number', 'like', "%{$this->search}%");
            }))
            ->when($this->transactionType, fn($q) => $q->where('transaction_type', $this->transactionType))
            ->when(!empty($this->dateRange) && count($this->dateRange ?? []) >= 2, fn($q) => $q->whereBetween('transaction_date', $this->dateRange))
            ->orderBy(...array_values($this->sort))
            ->paginate($this->quantity)
            ->withQueryString();
    }

    #[Computed]
    public function transactionTypeOptions(): array
    {
        return [
            ['label' => 'All Types', 'value' => ''],
            ['label' => 'Income', 'value' => 'credit'],
            ['label' => 'Expense', 'value' => 'debit'],
        ];
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->transactionType = '';
        $this->dateRange = [];
        $this->resetPage();
    }

    // Actions
    public function deleteTransaction($transactionId): void
    {
        $this->dispatch('delete-transaction', transactionId: $transactionId);
    }

    // Bulk operations
    public function confirmBulkDelete(): void
    {
        if (empty($this->selected)) {
            $this->toast()->warning('Warning', 'Please select transactions')->send();
            return;
        }

        $this->dialog()
            ->question('Delete Transactions?', 'Delete ' . count($this->selected) . ' selected transactions?')
            ->confirm('Delete All', 'executeBulkDelete')
            ->cancel()
            ->send();
    }

    public function executeBulkDelete(): void
    {
        $deletedCount = 0;
        foreach ($this->selected as $id) {
            $transaction = BankTransaction::find($id);
            if (!$transaction)
                continue;

            // Handle transfer pairs
            if ($transaction->reference_number && str_starts_with($transaction->reference_number, 'TRF')) {
                $deletedCount += BankTransaction::where('reference_number', $transaction->reference_number)->count();
                BankTransaction::where('reference_number', $transaction->reference_number)->delete();
            } else {
                $transaction->delete();
                $deletedCount++;
            }
        }

        $this->selected = [];
        $this->dispatch('transactions-updated');
        $this->toast()->success('Success', "Deleted {$deletedCount} transactions")->send();
    }

    public function exportSelected(): void
    {
        if (empty($this->selected)) {
            $this->toast()->warning('Warning', 'Please select transactions')->send();
            return;
        }

        $this->toast()->info('Export Started', 'Exporting ' . count($this->selected) . ' transactions')->send();
    }

    // Auto-reset pagination
    public function updatedSearch(): void
    {
        $this->resetPage();
    }
    public function updatedTransactionType(): void
    {
        $this->resetPage();
    }
    public function updatedDateRange(): void
    {
        $this->resetPage();
    }
}