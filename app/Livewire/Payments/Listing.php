<?php

namespace App\Livewire\Payments;

use App\Models\Payment;
use App\Models\BankAccount;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class Listing extends Component
{
    use WithPagination, Interactions;

    protected $listeners = [
        'payment-created' => '$refresh',
        'payment-deleted' => '$refresh',
        'payment-updated' => '$refresh',
    ];

    // Table properties
    public array $selected = [];
    public array $sort = ['column' => 'payment_date', 'direction' => 'desc'];
    public ?int $quantity = 10;

    // Filters
    public ?string $search = null;
    public ?string $paymentMethodFilter = null;
    public ?string $bankAccountFilter = null;
    public ?string $invoiceStatusFilter = null;
    public $dateRange = [];

    public function with(): array
    {
        return [
            'headers' => [
                ['index' => 'payment_date', 'label' => 'Tanggal'],
                ['index' => 'invoice_number', 'label' => 'Invoice'],
                ['index' => 'client_name', 'label' => 'Klien'],
                ['index' => 'amount', 'label' => 'Jumlah'],
                ['index' => 'payment_method', 'label' => 'Metode'],
                ['index' => 'bank_account', 'label' => 'Rekening'],
                ['index' => 'actions', 'label' => 'Aksi', 'sortable' => false],
            ],
            'rows' => $this->getPayments(),
            'bankAccounts' => BankAccount::select('id', 'bank_name', 'account_name')->orderBy('bank_name')->get(),
            'stats' => $this->calculateStats(),
        ];
    }

    private function getPayments()
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

        // Apply filters
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('invoices.invoice_number', 'like', "%{$this->search}%")
                    ->orWhere('clients.name', 'like', "%{$this->search}%")
                    ->orWhere('payments.reference_number', 'like', "%{$this->search}%");
            });
        }

        if ($this->paymentMethodFilter) {
            $query->where('payments.payment_method', $this->paymentMethodFilter);
        }

        if ($this->bankAccountFilter) {
            $query->where('payments.bank_account_id', $this->bankAccountFilter);
        }

        if ($this->invoiceStatusFilter) {
            $query->where('invoices.status', $this->invoiceStatusFilter);
        }

        // Date range filter
        if (!empty($this->dateRange) && is_array($this->dateRange) && count($this->dateRange) >= 2 && $this->dateRange[0] && $this->dateRange[1]) {
            $query->whereDate('payments.payment_date', '>=', $this->dateRange[0])
                ->whereDate('payments.payment_date', '<=', $this->dateRange[1]);
        }

        // Handle sorting
        if ($this->sort['column'] === 'invoice_number') {
            $query->orderBy('invoices.invoice_number', $this->sort['direction']);
        } elseif ($this->sort['column'] === 'client_name') {
            $query->orderBy('clients.name', $this->sort['direction']);
        } elseif ($this->sort['column'] === 'bank_account') {
            $query->orderBy('bank_accounts.bank_name', $this->sort['direction']);
        } elseif (in_array($this->sort['column'], ['payment_date', 'amount', 'payment_method'])) {
            $query->orderBy('payments.' . $this->sort['column'], $this->sort['direction']);
        } else {
            $query->orderBy(...array_values($this->sort));
        }

        return $query->paginate($this->quantity)->withQueryString();
    }

    private function calculateStats(): array
    {
        $baseQuery = Payment::query();
        $thisMonth = now()->month;
        $thisYear = now()->year;

        return [
            'total_payments' => $baseQuery->count(),
            'total_amount' => $baseQuery->sum('amount'),
            'this_month_count' => $baseQuery->whereMonth('payment_date', $thisMonth)->whereYear('payment_date', $thisYear)->count(),
            'this_month_amount' => $baseQuery->whereMonth('payment_date', $thisMonth)->whereYear('payment_date', $thisYear)->sum('amount'),
        ];
    }

    public function clearFilters(): void
    {
        $this->search = null;
        $this->paymentMethodFilter = null;
        $this->bankAccountFilter = null;
        $this->invoiceStatusFilter = null;
        $this->dateRange = [];
        $this->resetPage();
    }

    public function updatedPaymentMethodFilter(): void
    {
        $this->resetPage();
    }

    public function updatedBankAccountFilter(): void
    {
        $this->resetPage();
    }

    public function updatedInvoiceStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedDateRange(): void
    {
        $this->resetPage();
    }

    public function editPayment(int $paymentId): void
    {
        $this->dispatch('edit-payment', paymentId: $paymentId);
    }

    public function viewInvoice(int $invoiceId): void
    {
        $this->dispatch('show-invoice', invoiceId: $invoiceId);
    }

    public function exportExcel()
    {
        $service = new \App\Services\PaymentExportService();

        $filters = [
            'search' => $this->search,
            'paymentMethodFilter' => $this->paymentMethodFilter,
            'bankAccountFilter' => $this->bankAccountFilter,
            'invoiceStatusFilter' => $this->invoiceStatusFilter,
            'dateRange' => $this->dateRange,
        ];

        return $service->exportExcel($filters);
    }

    public function render()
    {
        return view('livewire.payments.listing', $this->with());
    }
}