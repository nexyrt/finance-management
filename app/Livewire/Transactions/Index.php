<?php

namespace App\Livewire\Transactions;

use App\Models\BankTransaction;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

class Index extends Component
{
    // Filter state from Listing component
    public ?string $account_id = null;
    public ?string $transaction_type = null;
    public ?string $search = null;
    public ?string $selected_month = null;
    public ?array $date_range = null;

    #[On('filter-changed')]
    public function updateFilters(array $filters): void
    {
        $this->account_id = $filters['account_id'];
        $this->transaction_type = $filters['transaction_type'];
        $this->search = $filters['search'];
        $this->selected_month = $filters['selected_month'];
        $this->date_range = $filters['date_range'];
    }

    #[On('transaction-deleted')]
    public function refreshStats(): void
    {
        // Force refresh computed property
        unset($this->stats);
    }

    #[Computed]
    public function stats()
    {
        $baseQuery = BankTransaction::query()
            ->when($this->account_id, fn($q) => $q->where('bank_account_id', $this->account_id))
            ->when($this->transaction_type, fn($q) => $q->where('transaction_type', $this->transaction_type))
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
            );

        return [
            'total_income' => $baseQuery->clone()->where('transaction_type', 'credit')->sum('amount'),
            'total_expense' => $baseQuery->clone()->where('transaction_type', 'debit')->sum('amount'),
            'total_transactions' => $baseQuery->clone()->count(),
        ];
    }

    public function openTransfer(): void
    {
        $this->dispatch('open-transfer-modal');
    }

    public function render()
    {
        return view('livewire.transactions.index');
    }
}