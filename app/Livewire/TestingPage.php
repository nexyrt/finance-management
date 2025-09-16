<?php

namespace App\Livewire;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Renderless;
use TallStackUi\Traits\Interactions;

class TestingPage extends Component
{
    use WithPagination, Interactions;

    // Table properties
    public ?string $search = null;
    public ?int $quantity = 10;
    public array $sort = ['column' => 'transaction_date', 'direction' => 'desc'];
    public array $selected = [];

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
        BankTransaction::whereIn('id', $this->selected)->delete();

        $this->selected = [];
        $this->resetPage();
        $this->toast()->success("{$count} transaksi berhasil dihapus")->send();
    }

    public function render()
    {
        return view('livewire.testing-page');
    }
}