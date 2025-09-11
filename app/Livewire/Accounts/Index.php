<?php

namespace App\Livewire\Accounts;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\Payment;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use TallStackUi\Traits\Interactions;

class Index extends Component
{
    use WithPagination, Interactions;

    // Selected account for main content
    public $selectedAccountId = null;

    // Tab management
    public string $activeTab = 'transactions';

    // Search and filters for main content
    public string $search = '';
    public string $transactionType = '';
    public array $dateRange = [];

    // Sorting for table
    public array $sort = [
        'column' => 'transaction_date',
        'direction' => 'desc',
    ];

    // Selection for bulk operations
    public array $selected = [];

    public function mount(): void
    {
        // Auto-select first account if available
        if ($this->accountsData->count() > 0) {
            $this->selectedAccountId = $this->accountsData->first()['id'];
        }
    }

    public function selectAccount($accountId = null): void
    {
        $this->selectedAccountId = $accountId;
        $this->selected = [];
        $this->resetPage();

        $this->toast()
            ->success('Account Selected', 'Viewing data for selected account')
            ->send();
    }

    public function switchTab($tab): void
    {
        $this->activeTab = $tab;
        $this->selected = [];
        $this->resetPage();
    }

    public function openCreateAccount(): void
    {
        $this->dispatch('open-create-account-modal');
    }

    public function openTransaction(): void
    {
        if (!$this->selectedAccountId) {
            $this->toast()->warning('Warning', 'Please select an account first')->send();
            return;
        }

        $this->dispatch('open-transaction-modal', accountId: $this->selectedAccountId);
    }

    public function openTransfer(): void
    {
        if (!$this->selectedAccountId) {
            $this->toast()->warning('Warning', 'Please select an account first')->send();
            return;
        }

        $this->dispatch('open-transfer-modal', fromAccountId: $this->selectedAccountId);
    }

    public function exportReport(): void
    {
        if (!$this->selectedAccountId) {
            $this->toast()->warning('Warning', 'Please select an account first')->send();
            return;
        }

        $this->toast()
            ->info('Export Started', 'Your report is being generated')
            ->send();
    }

    public function editAccount($accountId): void
    {
        $this->dispatch('edit-account', accountId: $accountId);
    }

    public function deleteAccount($accountId): void
    {
        $this->dispatch('delete-account', accountId: $accountId);
    }

    public function deleteTransaction($transactionId): void
    {
        $this->dispatch('delete-transaction', transactionId: $transactionId);
    }

    public function deletePayment($paymentId): void
    {
        $this->dispatch('delete-payment', paymentId: $paymentId);
    }

    #[On('account-created', 'account-updated', 'account-deleted', 'transaction-created', 'transaction-deleted', 'transfer-completed', 'payment-deleted')]
    public function refreshData(): void
    {
        $this->resetPage();
        $this->selected = [];

        $this->toast()
            ->success('Data Updated', 'Information has been refreshed')
            ->send();
    }

    public function bulkDelete(): void
    {
        if (empty($this->selected)) {
            $this->toast()->warning('Warning', 'Please select items to delete')->send();
            return;
        }

        $count = count($this->selected);

        if ($this->activeTab === 'transactions') {
            $this->bulkDeleteTransactions($count);
        } else {
            $this->bulkDeletePayments($count);
        }
    }

    private function bulkDeleteTransactions($count): void
    {
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
            ->confirm('Delete All', 'executeBulkDeleteTransactions')
            ->cancel('Cancel')
            ->send();
    }

    private function bulkDeletePayments($count): void
    {
        $selectedPayments = Payment::whereIn('id', $this->selected)->get();
        $totalAmount = $selectedPayments->sum('amount');

        $message = "Delete <strong>{$count} payments</strong>?<br><br>";
        $message .= "<div class='bg-zinc-50 dark:bg-dark-700 rounded-lg p-4 text-center'>";
        $message .= "<div class='text-sm text-dark-600'>Total Amount</div>";
        $message .= "<div class='font-bold text-lg'>Rp " . number_format($totalAmount, 0, ',', '.') . "</div>";
        $message .= "</div>";

        $this->dialog()
            ->question('Bulk Delete Payments?', $message)
            ->confirm('Delete All', 'executeBulkDeletePayments')
            ->cancel('Cancel')
            ->send();
    }

    public function executeBulkDeleteTransactions(): void
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
            $this->dispatch('$refresh');

            $this->toast()
                ->success('Success!', "Deleted {$deletedCount} transactions.")
                ->send();

        } catch (\Exception $e) {
            $this->toast()
                ->error('Failed!', 'Error occurred while deleting transactions.')
                ->send();
        }
    }

    public function executeBulkDeletePayments(): void
    {
        try {
            $deletedCount = Payment::whereIn('id', $this->selected)->count();
            Payment::whereIn('id', $this->selected)->delete();

            $this->selected = [];
            $this->dispatch('$refresh');

            $this->toast()
                ->success('Success!', "Deleted {$deletedCount} payments.")
                ->send();

        } catch (\Exception $e) {
            $this->toast()
                ->error('Failed!', 'Error occurred while deleting payments.')
                ->send();
        }
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->transactionType = '';
        $this->dateRange = [];
        $this->selected = [];
        $this->resetPage();

        $this->toast()
            ->info('Filters Cleared', 'All filters have been reset')
            ->send();
    }

    #[Computed]
    public function accountsData()
    {
        return BankAccount::with([
            'transactions' => function ($query) {
                $query->latest()->take(3);
            }
        ])->get()->map(function ($account) {
            return [
                'id' => $account->id,
                'name' => $account->account_name,
                'bank' => $account->bank_name,
                'account_number' => $account->account_number,
                'balance' => $account->balance,
                'recent_transactions' => $account->transactions,
                'trend' => $this->calculateTrend($account->id)
            ];
        });
    }

    #[Computed]
    public function totalBalance(): int
    {
        return $this->accountsData->sum('balance');
    }

    #[Computed]
    public function totalIncome(): int
    {
        return BankTransaction::where('transaction_type', 'credit')->sum('amount');
    }

    #[Computed]
    public function totalExpense(): int
    {
        return BankTransaction::where('transaction_type', 'debit')->sum('amount');
    }

    #[Computed]
    public function transactions()
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

        return $query->orderBy(...array_values($this->sort))->get();
    }

    #[Computed]
    public function payments()
    {
        $query = Payment::with(['invoice.client', 'bankAccount'])
            ->when($this->selectedAccountId, fn($q) => $q->where('bank_account_id', $this->selectedAccountId))
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('reference_number', 'like', "%{$this->search}%")
                        ->orWhereHas('invoice', fn($q) => $q->where('invoice_number', 'like', "%{$this->search}%"))
                        ->orWhereHas('invoice.client', fn($q) => $q->where('name', 'like', "%{$this->search}%"));
                });
            })
            ->when(!empty($this->dateRange) && count($this->dateRange) >= 2, function ($q) {
                $q->whereBetween('payment_date', $this->dateRange);
            });

        // Adjust sort column for payments
        $sortColumn = $this->sort['column'] === 'transaction_date' ? 'payment_date' : $this->sort['column'];
        return $query->orderBy($sortColumn, $this->sort['direction'])->get();
    }

    #[Computed]
    public function transactionHeaders(): array
    {
        return [
            ['index' => 'description', 'label' => 'Description'],
            ['index' => 'reference_number', 'label' => 'Reference'],
            ['index' => 'transaction_date', 'label' => 'Date'],
            ['index' => 'amount', 'label' => 'Amount'],
            ['index' => 'action', 'label' => 'Action'],
        ];
    }

    #[Computed]
    public function paymentHeaders(): array
    {
        return [
            ['index' => 'invoice', 'label' => 'Invoice'],
            ['index' => 'client', 'label' => 'Client'],
            ['index' => 'payment_date', 'label' => 'Date'],
            ['index' => 'amount', 'label' => 'Amount'],
            ['index' => 'payment_method', 'label' => 'Method'],
            ['index' => 'action', 'label' => 'Action'],
        ];
    }

    private function calculateTrend($accountId): string
    {
        $thisMonth = BankTransaction::where('bank_account_id', $accountId)
            ->where('transaction_type', 'credit')
            ->whereMonth('transaction_date', now()->month)
            ->sum('amount');
        $lastMonth = BankTransaction::where('bank_account_id', $accountId)
            ->where('transaction_type', 'credit')
            ->whereMonth('transaction_date', now()->subMonth()->month)
            ->sum('amount');

        return $thisMonth >= $lastMonth ? 'up' : 'down';
    }

    public function render()
    {
        return view('livewire.accounts.index');
    }
}