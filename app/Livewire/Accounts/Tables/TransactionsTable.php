<?php

namespace App\Livewire\Accounts\Tables;

use App\Models\BankTransaction;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Renderless;
use TallStackUi\Traits\Interactions;

class TransactionsTable extends Component
{
    use WithPagination, Interactions;

    // Parent props
    public $selectedAccountId;
    public string $search = '';
    public string $transactionType = '';
    public array $dateRange = [];

    // Table specific props
    public array $sort = [
        'column' => 'transaction_date',
        'direction' => 'desc',
    ];
    public array $selected = [];
    public ?int $quantity = 10;

    // Static headers
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

    // Data loading
    #[Computed]
    public function rows()
    {
        $query = BankTransaction::with('bankAccount')
            ->when($this->selectedAccountId, fn($q) => $q->where('bank_account_id', $this->selectedAccountId))
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('description', 'like', "%{$this->search}%")
                        ->orWhere('reference_number', 'like', "%{$this->search}%");
                });
            })
            ->when($this->transactionType, fn($q) => $q->where('transaction_type', $this->transactionType))
            ->when(!empty($this->dateRange) && count($this->dateRange) >= 2, function ($q) {
                $q->whereBetween('transaction_date', $this->dateRange);
            });

        return $query->orderBy(...array_values($this->sort))
            ->paginate($this->quantity)
            ->withQueryString();
    }

    // Actions
    public function deleteTransaction($transactionId): void
    {
        $this->dispatch('delete-transaction', transactionId: $transactionId);
    }

    // Bulk operations
    #[Renderless]
    public function confirmBulkDelete(): void
    {
        if (empty($this->selected)) {
            $this->toast()->warning('Warning', 'Please select transactions to delete')->send();
            return;
        }

        $count = count($this->selected);
        $selectedTransactions = BankTransaction::whereIn('id', $this->selected)->get();
        $totalAmount = $selectedTransactions->sum('amount');
        $incomeCount = $selectedTransactions->where('transaction_type', 'credit')->count();
        $expenseCount = $selectedTransactions->where('transaction_type', 'debit')->count();

        $message = "Delete <strong>{$count} transactions</strong>?<br><br>";
        $message .= "<div class='bg-zinc-50 dark:bg-dark-700 rounded-lg p-4'>";
        $message .= "<div class='grid grid-cols-3 gap-4 text-center'>";
        $message .= "<div><div class='text-sm text-dark-600'>Total</div><div class='font-bold'>{$count}</div></div>";
        $message .= "<div><div class='text-sm text-green-600'>Income</div><div class='font-bold text-green-600'>{$incomeCount}</div></div>";
        $message .= "<div><div class='text-sm text-red-600'>Expense</div><div class='font-bold text-red-600'>{$expenseCount}</div></div>";
        $message .= "</div></div>";

        $this->dialog()
            ->question('Bulk Delete Transactions?', $message)
            ->confirm('Delete All', 'executeBulkDelete')
            ->cancel('Cancel')
            ->send();
    }

    public function executeBulkDelete(): void
    {
        try {
            $deletedCount = 0;
            foreach ($this->selected as $transactionId) {
                $transaction = BankTransaction::find($transactionId);
                if (!$transaction)
                    continue;

                if ($transaction->reference_number && str_starts_with($transaction->reference_number, 'TRF')) {
                    $deleted = BankTransaction::where('reference_number', $transaction->reference_number)->delete();
                    $deletedCount += $deleted;
                } else {
                    $transaction->delete();
                    $deletedCount++;
                }
            }

            $this->selected = [];
            $this->dispatch('transactions-updated');

            $this->toast()
                ->success('Success!', "Deleted {$deletedCount} transactions.")
                ->send();

        } catch (\Exception $e) {
            $this->toast()
                ->error('Failed!', 'Error occurred while deleting transactions.')
                ->send();
        }
    }

    public function exportSelected(): void
    {
        if (empty($this->selected)) {
            $this->toast()->warning('Warning', 'Please select transactions to export')->send();
            return;
        }

        $count = count($this->selected);
        $this->toast()
            ->info('Export Started', "Exporting {$count} transactions...")
            ->send();
    }

    // Listen to parent updates
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