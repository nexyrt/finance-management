<?php

namespace App\Livewire\Payments;

use App\Models\Payment;
use App\Models\BankAccount;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use TallStackUi\Traits\Interactions;
use Illuminate\Pagination\LengthAwarePaginator;

class Listing extends Component
{
    use WithPagination, Interactions;

    // Optional bank account constraint
    #[Reactive]
    public ?int $constrainedBankAccountId = null;

    // Table properties
    public array $selected = [];
    public array $sort = ['column' => 'payment_date', 'direction' => 'desc'];
    public ?int $quantity = 25;

    // Filters
    public ?string $paymentMethodFilter = null;
    public ?string $bankAccountFilter = null;
    public ?string $invoiceStatusFilter = null;
    public ?string $selectedMonth = null;
    public ?string $search = null;
    public $dateRange = [];

    public array $headers = [
        ['index' => 'payment_date', 'label' => '#'],
        ['index' => 'invoice_number', 'label' => '#'],
        ['index' => 'client_name', 'label' => '#'],
        ['index' => 'amount', 'label' => '#'],
        ['index' => 'payment_method', 'label' => '#'],
        ['index' => 'bank_account', 'label' => '#'],
        ['index' => 'actions', 'label' => '#', 'sortable' => false],
    ];

    public function mount()
    {
        // Translate headers
        $this->headers = [
            ['index' => 'payment_date', 'label' => __('pages.date')],
            ['index' => 'invoice_number', 'label' => __('pages.invoice')],
            ['index' => 'client_name', 'label' => __('pages.client')],
            ['index' => 'amount', 'label' => __('common.amount')],
            ['index' => 'payment_method', 'label' => __('pages.method')],
            ['index' => 'bank_account', 'label' => __('pages.account')],
            ['index' => 'actions', 'label' => __('common.actions'), 'sortable' => false],
        ];

        $this->dateRange = [];

        // Auto-set bank account filter if constrained
        if ($this->constrainedBankAccountId) {
            $this->bankAccountFilter = (string) $this->constrainedBankAccountId;
        }

        $this->dispatchFilterChange();
    }

    #[Computed]
    public function payments(): LengthAwarePaginator
    {
        $query = Payment::query()
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->join('clients', 'invoices.billed_to_id', '=', 'clients.id')
            ->join('bank_accounts', 'payments.bank_account_id', '=', 'bank_accounts.id')
            ->select([
                'payments.*',
                'invoices.invoice_number',
                'invoices.status as invoice_status',
                'clients.name as client_name',
                'clients.type as client_type',
                'bank_accounts.bank_name',
                'bank_accounts.account_name',
            ]);

        // Search filter - tambahkan ini setelah select
        $query->when(
            $this->search,
            fn($q) =>
            $q->where(function ($searchQuery) {
                $searchQuery->where('invoices.invoice_number', 'like', '%' . $this->search . '%')
                    ->orWhere('clients.name', 'like', '%' . $this->search . '%')
                    ->orWhere('payments.reference_number', 'like', '%' . $this->search . '%')
                    ->orWhere('bank_accounts.bank_name', 'like', '%' . $this->search . '%')
                    ->orWhere('bank_accounts.account_name', 'like', '%' . $this->search . '%');
            })
        );

        // Filters (with constraint) - sisanya tetap sama
        $query->when($this->paymentMethodFilter, fn($q) => $q->where('payments.payment_method', $this->paymentMethodFilter));

        // Bank account filter - constrained or user-selected
        $bankAccountId = $this->constrainedBankAccountId ?? $this->bankAccountFilter;
        $query->when($bankAccountId, fn($q) => $q->where('payments.bank_account_id', $bankAccountId));

        $query->when($this->invoiceStatusFilter, fn($q) => $q->where('invoices.status', $this->invoiceStatusFilter));

        // Date filtering - range overrides month
        $query->when(
            $this->dateRange && count($this->dateRange) >= 2 && $this->dateRange[0] && $this->dateRange[1],
            fn($q) => $q->whereBetween('payments.payment_date', [
                $this->dateRange[0],
                $this->dateRange[1]
            ])
        )->unless(
                $this->dateRange,
                fn($q) => $q->when(
                    $this->selectedMonth,
                    fn($query) => $query->whereYear('payments.payment_date', substr($this->selectedMonth, 0, 4))
                        ->whereMonth('payments.payment_date', substr($this->selectedMonth, 5, 2))
                )
            );

        // Sorting
        match ($this->sort['column']) {
            'invoice_number' => $query->orderBy('invoices.invoice_number', $this->sort['direction']),
            'client_name' => $query->orderBy('clients.name', $this->sort['direction']),
            'bank_account' => $query->orderBy('bank_accounts.bank_name', $this->sort['direction']),
            'payment_date', 'amount', 'payment_method' =>
            $query->orderBy('payments.' . $this->sort['column'], $this->sort['direction']),
            default => $query->orderBy(...array_values($this->sort))
        };

        return $query->paginate($this->quantity)->withQueryString();
    }

    #[Computed]
    public function bankAccounts()
    {
        return BankAccount::select('id', 'bank_name', 'account_name')->orderBy('bank_name')->get();
    }

    // Filter change dispatcher
    protected function dispatchFilterChange(): void
    {
        $this->dispatch('filter-changed', [
            'search' => $this->search, // tambahkan ini
            'paymentMethodFilter' => $this->paymentMethodFilter,
            'bankAccountFilter' => $this->bankAccountFilter,
            'invoiceStatusFilter' => $this->invoiceStatusFilter,
            'selectedMonth' => $this->selectedMonth,
            'dateRange' => $this->dateRange,
        ]);
    }

    // Filter watchers
    public function updatedPaymentMethodFilter()
    {
        $this->resetPage();
        $this->dispatchFilterChange();
    }

    public function updatedBankAccountFilter()
    {
        $this->resetPage();
        $this->dispatchFilterChange();
    }

    public function updatedInvoiceStatusFilter()
    {
        $this->resetPage();
        $this->dispatchFilterChange();
    }

    public function updatedSelectedMonth()
    {
        $this->resetPage();
        $this->dispatchFilterChange();
    }

    public function updatedDateRange()
    {
        $this->resetPage();
        $this->dispatchFilterChange();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->dispatchFilterChange();
    }

    // Action methods for loading states
    public function editPayment(int $paymentId): void
    {
        $this->dispatch('edit-payment', paymentId: $paymentId);
    }

    public function viewInvoice(int $invoiceId): void
    {
        $this->dispatch('show-invoice', invoiceId: $invoiceId);
    }

    public function deletePayment(int $paymentId): void
    {
        $this->dispatch('delete-payment', paymentId: $paymentId);
    }

    public function exportExcel()
    {
        return (new \App\Services\PaymentExportService())->exportExcel($this->getFilters());
    }

    public function exportPdf()
    {
        $service = new \App\Services\PaymentExportService();
        return response()->streamDownload(
            fn() => print $service->exportPdf($this->getFilters())->output(),
            'payments-' . now()->format('Y-m-d') . '.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }

    private function getFilters(): array
    {
        return [
            'paymentMethodFilter' => $this->paymentMethodFilter,
            'bankAccountFilter' => $this->bankAccountFilter,
            'invoiceStatusFilter' => $this->invoiceStatusFilter,
            'selectedMonth' => $this->selectedMonth,
            'dateRange' => $this->dateRange,
        ];
    }

    public function render()
    {
        return view('livewire.payments.listing');
    }
}