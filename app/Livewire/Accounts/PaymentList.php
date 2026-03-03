<?php

namespace App\Livewire\Accounts;

use App\Models\Payment;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class PaymentList extends Component
{
    use Interactions, WithPagination;

    public ?int $selectedAccountId = null;

    // Table properties
    public ?string $search = null;

    public ?int $quantity = 15;

    public array $sort = ['column' => 'payment_date', 'direction' => 'desc'];

    public array $selected = [];

    // Filters
    public ?string $paymentMethodFilter = null;

    public ?string $invoiceStatusFilter = null;

    public ?string $selectedMonth = null;

    public array $headers = [];

    public function mount(?int $selectedAccountId = null): void
    {
        $this->selectedAccountId = $selectedAccountId;

        $this->headers = [
            ['index' => 'payment_date', 'label' => __('pages.date')],
            ['index' => 'invoice_number', 'label' => __('pages.invoice')],
            ['index' => 'client_name', 'label' => __('pages.client')],
            ['index' => 'amount', 'label' => __('common.amount')],
            ['index' => 'payment_method', 'label' => __('pages.method')],
            ['index' => 'actions', 'label' => '', 'sortable' => false],
        ];
    }

    #[On('account-selected')]
    public function handleAccountChange(int $accountId): void
    {
        $this->selectedAccountId = $accountId;
        $this->reset(['search', 'paymentMethodFilter', 'invoiceStatusFilter', 'selectedMonth', 'selected']);
        $this->resetPage();
    }

    #[Computed]
    public function payments(): LengthAwarePaginator
    {
        if (! $this->selectedAccountId) {
            return new LengthAwarePaginator([], 0, $this->quantity);
        }

        $query = Payment::query()
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->join('clients', 'invoices.billed_to_id', '=', 'clients.id')
            ->select([
                'payments.*',
                'invoices.invoice_number',
                'invoices.status as invoice_status',
                'clients.name as client_name',
                'clients.type as client_type',
            ])
            ->where('payments.bank_account_id', $this->selectedAccountId);

        // Search
        $query->when($this->search, fn ($q) => $q->where(function ($searchQuery) {
            $searchQuery->where('invoices.invoice_number', 'like', "%{$this->search}%")
                ->orWhere('clients.name', 'like', "%{$this->search}%")
                ->orWhere('payments.reference_number', 'like', "%{$this->search}%");
        }));

        // Filters
        $query->when($this->paymentMethodFilter, fn ($q) => $q->where('payments.payment_method', $this->paymentMethodFilter));
        $query->when($this->invoiceStatusFilter, fn ($q) => $q->where('invoices.status', $this->invoiceStatusFilter));
        $query->when($this->selectedMonth, fn ($q) => $q
            ->whereYear('payments.payment_date', substr($this->selectedMonth, 0, 4))
            ->whereMonth('payments.payment_date', substr($this->selectedMonth, 5, 2))
        );

        // Sorting
        match ($this->sort['column']) {
            'invoice_number' => $query->orderBy('invoices.invoice_number', $this->sort['direction']),
            'client_name' => $query->orderBy('clients.name', $this->sort['direction']),
            'payment_date', 'amount', 'payment_method' => $query->orderBy('payments.'.$this->sort['column'], $this->sort['direction']),
            default => $query->orderBy('payments.payment_date', $this->sort['direction']),
        };

        return $query->paginate($this->quantity);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPaymentMethodFilter(): void
    {
        $this->resetPage();
    }

    public function updatedInvoiceStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedMonth(): void
    {
        $this->resetPage();
    }

    public function deletePayment(int $paymentId): void
    {
        $this->dispatch('delete-payment', paymentId: $paymentId);
    }

    #[Renderless]
    public function confirmBulkDelete(): void
    {
        if (empty($this->selected)) {
            return;
        }

        $count = count($this->selected);
        $this->dialog()
            ->question(__('pages.pmt_bulk_delete_title', ['count' => $count]), __('pages.pmt_bulk_delete_message'))
            ->confirm(method: 'bulkDelete')
            ->cancel()
            ->send();
    }

    public function bulkDelete(): void
    {
        if (empty($this->selected)) {
            return;
        }

        $count = count($this->selected);
        Payment::whereIn('id', $this->selected)->delete();

        $this->selected = [];
        $this->resetPage();
        $this->toast()->success(__('pages.pmt_bulk_delete_success', ['count' => $count]))->send();

        $this->dispatch('payment-deleted');
    }

    public function render()
    {
        return view('livewire.accounts.payment-list');
    }
}
