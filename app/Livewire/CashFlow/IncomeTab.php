<?php

namespace App\Livewire\CashFlow;

use App\Models\BankTransaction;
use App\Models\Payment;
use App\Models\BankAccount;
use App\Models\TransactionCategory;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Illuminate\Support\Collection;

class IncomeTab extends Component
{
    use WithPagination;

    public ?string $startDate = null;
    public ?string $endDate = null;
    public ?int $categoryId = null;
    public ?int $bankAccountId = null;
    public ?string $search = null;

    public function mount(): void
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    #[Computed]
    public function categories(): array
    {
        return TransactionCategory::where('type', 'income')
            ->orderBy('label')
            ->get()
            ->map(fn($cat) => ['label' => $cat->label, 'value' => $cat->id])
            ->toArray();
    }

    #[Computed]
    public function bankAccounts(): array
    {
        return BankAccount::orderBy('account_name')
            ->get()
            ->map(fn($acc) => ['label' => $acc->account_name, 'value' => $acc->id])
            ->toArray();
    }

    #[Computed]
    public function incomeTransactions(): Collection
    {
        // Get BankTransactions (credit)
        $bankTransactions = BankTransaction::with(['bankAccount', 'category'])
            ->where('transaction_type', 'credit')
            ->when($this->startDate, fn($q) => $q->whereDate('transaction_date', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('transaction_date', '<=', $this->endDate))
            ->when($this->categoryId, fn($q) => $q->where('category_id', $this->categoryId))
            ->when($this->bankAccountId, fn($q) => $q->where('bank_account_id', $this->bankAccountId))
            ->when($this->search, fn($q) => $q->where(function($query) {
                $query->where('description', 'like', "%{$this->search}%")
                    ->orWhere('reference_number', 'like', "%{$this->search}%");
            }))
            ->get()
            ->map(function($transaction) {
                return [
                    'id' => $transaction->id,
                    'type' => 'bank_transaction',
                    'date' => $transaction->transaction_date,
                    'description' => $transaction->description ?? '-',
                    'category' => $transaction->category?->label ?? '-',
                    'category_type' => $transaction->category?->type ?? null,
                    'bank_account' => $transaction->bankAccount->account_name,
                    'amount' => $transaction->amount,
                    'reference' => $transaction->reference_number,
                ];
            });

        // Get Payments
        $payments = Payment::with(['invoice.client', 'bankAccount'])
            ->when($this->startDate, fn($q) => $q->whereDate('payment_date', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('payment_date', '<=', $this->endDate))
            ->when($this->bankAccountId, fn($q) => $q->where('bank_account_id', $this->bankAccountId))
            ->when($this->search, fn($q) => $q->where(function($query) {
                $query->where('reference_number', 'like', "%{$this->search}%")
                    ->orWhereHas('invoice.client', fn($q) => $q->where('name', 'like', "%{$this->search}%"));
            }))
            ->get()
            ->map(function($payment) {
                return [
                    'id' => $payment->id,
                    'type' => 'payment',
                    'date' => $payment->payment_date,
                    'description' => 'Payment from ' . $payment->invoice->client->name,
                    'category' => 'Invoice Payment',
                    'category_type' => 'income',
                    'bank_account' => $payment->bankAccount->account_name,
                    'amount' => $payment->amount,
                    'reference' => $payment->reference_number,
                ];
            });

        // Merge and sort by date
        return $bankTransactions->concat($payments)
            ->sortByDesc('date')
            ->values();
    }

    #[Computed]
    public function totalIncome(): int
    {
        return $this->incomeTransactions->sum('amount');
    }

    public function applyFilters(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['startDate', 'endDate', 'categoryId', 'bankAccountId', 'search']);
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.cash-flow.income-tab');
    }
}