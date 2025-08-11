<?php

namespace App\Livewire\Transactions;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class Index extends Component
{
    use WithPagination, Interactions;

    // Filters
    public $search = '';
    public $account_id = '';
    public $transaction_type = '';
    public $dateRange = [];

    // Sorting
    public array $sort = [
        'column' => 'transaction_date',
        'direction' => 'desc',
    ];

    // Selection for bulk actions
    public array $selected = [];

    public function mount()
    {
        $this->resetPage();
    }

    #[On('transaction-created', 'transaction-deleted', 'transfer-completed')]
    public function refreshData()
    {
        $this->selected = [];
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->account_id = '';
        $this->transaction_type = '';
        $this->dateRange = [];
        $this->selected = [];
        $this->resetPage();
    }

    public function bulkDelete()
    {
        if (empty($this->selected)) {
            $this->toast()->error('Error', 'Tidak ada transaksi yang dipilih.')->send();
            return;
        }

        $count = count($this->selected);
        $selectedTransactions = BankTransaction::whereIn('id', $this->selected)->get();
        $totalAmount = $selectedTransactions->sum('amount');

        $message = "Yakin ingin menghapus <strong>{$count} transaksi</strong> yang dipilih?";
        $message .= "<br><br><div class='bg-zinc-50 dark:bg-dark-700 rounded-lg p-4 my-3'>";
        $message .= "<div class='text-center'><div class='font-bold text-lg'>Rp " . number_format($totalAmount, 0, ',', '.') . "</div></div>";
        $message .= "</div>";
        $message .= "<div class='text-sm text-red-600 dark:text-red-400'><strong>Peringatan:</strong> Transfer akan dihapus beserta pasangannya.</div>";

        $this->dialog()
            ->question('Hapus Transaksi Massal?', $message)
            ->confirm('Hapus Semua', 'executeBulkDelete', "Berhasil menghapus {$count} transaksi")
            ->cancel('Batal')
            ->send();
    }

    public function executeBulkDelete()
    {
        try {
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
            $this->refreshData();

            $this->toast()
                ->success('Berhasil!', 'Transaksi berhasil dihapus.')
                ->send();

        } catch (\Exception $e) {
            $this->toast()
                ->error('Gagal!', 'Terjadi kesalahan saat menghapus transaksi.')
                ->send();
        }
    }

    public function with(): array
    {
        return [
            'transactions' => $this->getTransactions(),
            'accounts' => BankAccount::orderBy('account_name')->get(),
            'headers' => $this->getTableHeaders(),
            'stats' => $this->getStats(),
        ];
    }

    private function getTransactions()
    {
        $query = BankTransaction::with('bankAccount')
            ->when($this->account_id, fn($q) => $q->where('bank_account_id', $this->account_id))
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('description', 'like', "%{$this->search}%")
                        ->orWhere('reference_number', 'like', "%{$this->search}%");
                });
            })
            ->when($this->transaction_type, fn($q) => $q->where('transaction_type', $this->transaction_type))
            ->when(!empty($this->dateRange) && count($this->dateRange) >= 2, function ($q) {
                $q->whereBetween('transaction_date', $this->dateRange);
            });

        return $query->orderBy(...array_values($this->sort))->get();
    }

    private function getTableHeaders()
    {
        return [
            ['index' => 'description', 'label' => 'Deskripsi'],
            ['index' => 'bank_account_id', 'label' => 'Rekening'],
            ['index' => 'transaction_date', 'label' => 'Tanggal'],
            ['index' => 'amount', 'label' => 'Jumlah'],
            ['index' => 'action', 'label' => 'Aksi'],
        ];
    }

    private function getStats()
    {
        $baseQuery = BankTransaction::query()
            ->when($this->account_id, fn($q) => $q->where('bank_account_id', $this->account_id))
            ->when(!empty($this->dateRange) && count($this->dateRange) >= 2, function ($q) {
                $q->whereBetween('transaction_date', $this->dateRange);
            });

        return [
            'total_income' => $baseQuery->clone()->where('transaction_type', 'credit')->sum('amount'),
            'total_expense' => $baseQuery->clone()->where('transaction_type', 'debit')->sum('amount'),
            'total_transactions' => $baseQuery->clone()->count(),
        ];
    }

    public function render()
    {
        return view('livewire.transactions.index', $this->with());
    }
}