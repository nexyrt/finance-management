<?php

namespace App\Livewire\Accounts;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class Index extends Component
{
    use WithPagination, Interactions;

    // Selected account for main content
    public $selectedAccountId = null;

    // Search and filters for main content
    public $search = '';
    public $transactionType = '';
    public $dateRange = [];

    // Sorting for table
    public array $sort = [
        'column' => 'transaction_date',
        'direction' => 'desc',
    ];

    // Selection for bulk operations
    public array $selected = [];

    // Stats cache
    public $accountsData = [];
    public $totalBalance = 0;
    public $totalIncome = 0;
    public $totalExpense = 0;

    public function mount()
    {
        $this->calculateStats();

        // Auto-select first account if available
        if ($this->accountsData->count() > 0) {
            $this->selectedAccountId = $this->accountsData->first()['id'];
        }
    }

    public function selectAccount($accountId = null)
    {
        $this->selectedAccountId = $accountId;
        $this->selected = []; // Reset selection when changing account
        $this->resetPage();
    }

    #[On('account-created', 'account-updated', 'account-deleted', 'transaction-created', 'transaction-deleted', 'transfer-completed')]
    public function refreshData()
    {
        $this->resetPage();
        $this->selected = []; // Reset selection on refresh
        $this->calculateStats();
    }

    public function bulkDelete()
    {
        if (empty($this->selected)) {
            $this->toast()->error('Error', 'Tidak ada transaksi yang dipilih.')->send();
            return;
        }

        $count = count($this->selected);
        $selectedTransactions = BankTransaction::whereIn('id', $this->selected)->get();

        // Calculate total amount for preview
        $totalAmount = $selectedTransactions->sum('amount');
        $incomeCount = $selectedTransactions->where('transaction_type', 'credit')->count();
        $expenseCount = $selectedTransactions->where('transaction_type', 'debit')->count();

        $message = "Yakin ingin menghapus <strong>{$count} transaksi</strong> yang dipilih?";
        $message .= "<br><br><div class='bg-zinc-50 dark:bg-dark-700 rounded-lg p-4 my-3'>";
        $message .= "<div class='grid grid-cols-3 gap-4 text-center'>";
        $message .= "<div><div class='text-sm text-dark-600 dark:text-dark-400'>Total</div><div class='font-bold'>{$count}</div></div>";
        $message .= "<div><div class='text-sm text-green-600 dark:text-green-400'>Pemasukan</div><div class='font-bold text-green-600'>{$incomeCount}</div></div>";
        $message .= "<div><div class='text-sm text-red-600 dark:text-red-400'>Pengeluaran</div><div class='font-bold text-red-600'>{$expenseCount}</div></div>";
        $message .= "</div>";
        $message .= "<div class='mt-2 pt-2 border-t border-zinc-200 dark:border-dark-600 text-center'>";
        $message .= "<div class='text-xs text-dark-500 dark:text-dark-400'>Total Nilai</div>";
        $message .= "<div class='font-bold text-lg'>Rp " . number_format($totalAmount, 0, ',', '.') . "</div>";
        $message .= "</div></div>";
        $message .= "<div class='text-sm text-red-600 dark:text-red-400'><strong>Peringatan:</strong> Aksi ini akan mempengaruhi saldo rekening dan tidak dapat dibatalkan.</div>";

        $this->dialog()
            ->question('Hapus Transaksi Massal?', $message)
            ->confirm('Hapus Semua', 'executeBulkDelete', "Berhasil menghapus {$count} transaksi")
            ->cancel('Batal')
            ->send();
    }

    public function executeBulkDelete()
    {
        try {
            $deletedCount = 0;

            foreach ($this->selected as $transactionId) {
                $transaction = BankTransaction::find($transactionId);

                if (!$transaction)
                    continue;

                // Check if this is a transfer transaction
                if ($transaction->reference_number && str_starts_with($transaction->reference_number, 'TRF')) {
                    // Delete all transactions with same reference number
                    $deleted = BankTransaction::where('reference_number', $transaction->reference_number)->delete();
                    $deletedCount += $deleted;
                } else {
                    // Regular single transaction delete
                    $transaction->delete();
                    $deletedCount++;
                }
            }

            $this->selected = [];
            $this->refreshData();

            $this->toast()
                ->success('Berhasil!', "Berhasil menghapus transaksi dan transfer terkait.")
                ->send();

        } catch (\Exception $e) {
            $this->toast()
                ->error('Gagal!', 'Terjadi kesalahan saat menghapus transaksi.')
                ->send();
        }
    }

    private function calculateStats()
    {
        $this->accountsData = BankAccount::with([
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

        $this->totalBalance = $this->accountsData->sum('balance');
        $this->totalIncome = BankTransaction::where('transaction_type', 'credit')->sum('amount');
        $this->totalExpense = BankTransaction::where('transaction_type', 'debit')->sum('amount');
    }

    private function calculateTrend($accountId)
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

    public function with(): array
    {
        return [
            'transactions' => $this->getTransactions(),
            'headers' => $this->getTableHeaders(),
        ];
    }

    private function getTransactions()
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

        return $query->orderBy(...array_values($this->sort))->paginate(10);
    }

    private function getTableHeaders()
    {
        return [
            ['index' => 'description', 'label' => 'Deskripsi'],
            ['index' => 'reference_number', 'label' => 'Referensi'],
            ['index' => 'transaction_date', 'label' => 'Tanggal'],
            ['index' => 'amount', 'label' => 'Jumlah'],
            ['index' => 'action', 'label' => 'Aksi'],
        ];
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->transactionType = '';
        $this->dateRange = [];
        $this->selected = []; // Reset selection when clearing filters
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.accounts.index', $this->with());
    }
}