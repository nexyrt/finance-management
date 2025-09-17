<?php

namespace App\Livewire\Transactions;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Renderless;
use TallStackUi\Traits\Interactions;

class Index extends Component
{
    use WithPagination, Interactions;

    // Table properties
    public ?string $search = null;
    public ?int $quantity = 10;
    public array $sort = ['column' => 'transaction_date', 'direction' => 'desc'];
    public array $selected = [];

    // Filters
    public ?string $account_id = null;
    public ?string $transaction_type = null;

    // Attachment modal
    public bool $attachmentModal = false;
    public ?BankTransaction $selectedTransaction = null;

    public array $headers = [
        ['index' => 'description', 'label' => 'Transaction'],
        ['index' => 'bank_account_id', 'label' => 'Bank Account', 'sortable' => false],
        ['index' => 'transaction_date', 'label' => 'Date'],
        ['index' => 'amount', 'label' => 'Amount'],
        ['index' => 'action', 'sortable' => false],
    ];

    #[Computed]
    public function transactions()
    {
        return BankTransaction::with('bankAccount')
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
                $this->account_id,
                fn($query) =>
                $query->where('bank_account_id', $this->account_id)
            )
            ->when(
                $this->transaction_type,
                fn($query) =>
                $query->where('transaction_type', $this->transaction_type)
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
    public function stats()
    {
        $baseQuery = BankTransaction::query()
            ->when($this->account_id, fn($q) => $q->where('bank_account_id', $this->account_id));

        return [
            'total_income' => $baseQuery->clone()->where('transaction_type', 'credit')->sum('amount'),
            'total_expense' => $baseQuery->clone()->where('transaction_type', 'debit')->sum('amount'),
            'total_transactions' => $baseQuery->clone()->count(),
        ];
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
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'account_id', 'transaction_type', 'selected']);
        $this->resetPage();
    }

    public function openTransfer(): void
    {
        $this->dispatch('open-transfer-modal');
    }

    public function deleteTransaction(int $transactionId): void
    {
        $this->dispatch('delete-transaction', transactionId: $transactionId);
    }

    public function render()
    {
        return view('livewire.transactions.index');
    }
}