<?php

namespace App\Livewire\Accounts\Tables;

use App\Models\Payment;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use TallStackUi\Traits\Interactions;

class PaymentsTable extends Component
{
    use WithPagination, Interactions;

    public $selectedAccountId;

    // Internal filters
    public $search = '';
    public $dateRange = [];

    // Table state
    public array $sort = ['column' => 'payment_date', 'direction' => 'desc'];
    public array $selected = [];
    public ?int $quantity = 50;

    // Headers
    public array $headers = [
        ['index' => 'invoice', 'label' => 'Invoice'],
        ['index' => 'client', 'label' => 'Client'],
        ['index' => 'payment_date', 'label' => 'Date'],
        ['index' => 'amount', 'label' => 'Amount'],
        ['index' => 'payment_method', 'label' => 'Method'],
        ['index' => 'action', 'label' => 'Action', 'sortable' => false],
    ];

    public function render()
    {
        return view('livewire.accounts.tables.payments-table');
    }

    #[On('account-selected')]
    public function handleAccountChange($accountId): void
    {
        $this->selectedAccountId = $accountId;
        $this->clearFilters();
        $this->resetPage();
    }

    #[Computed]
    public function rows()
    {
        if (!$this->selectedAccountId)
            return collect();

        $query = Payment::with(['invoice.client', 'bankAccount'])
            ->where('bank_account_id', $this->selectedAccountId)
            ->when($this->search, fn($q) => $q->where(function ($query) {
                $query->where('reference_number', 'like', "%{$this->search}%")
                    ->orWhereHas('invoice', fn($q) => $q->where('invoice_number', 'like', "%{$this->search}%"))
                    ->orWhereHas('invoice.client', fn($q) => $q->where('name', 'like', "%{$this->search}%"));
            }))
            ->when(!empty($this->dateRange) && count($this->dateRange ?? []) >= 2, fn($q) => $q->whereBetween('payment_date', $this->dateRange));

        // Handle sorting for relationship fields
        if ($this->sort['column'] === 'invoice') {
            $query->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
                ->orderBy('invoices.invoice_number', $this->sort['direction'])
                ->select('payments.*');
        } elseif ($this->sort['column'] === 'client') {
            $query->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
                ->join('clients', 'invoices.billed_to_id', '=', 'clients.id')
                ->orderBy('clients.name', $this->sort['direction'])
                ->select('payments.*');
        } else {
            $query->orderBy(...array_values($this->sort));
        }

        return $query->paginate($this->quantity)->withQueryString();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->dateRange = [];
        $this->resetPage();
    }

    // Actions
    public function deletePayment($paymentId): void
    {
        $this->dispatch('delete-payment', paymentId: $paymentId);
    }

    // Bulk operations
    public function confirmBulkDelete(): void
    {
        if (empty($this->selected)) {
            $this->toast()->warning('Warning', 'Please select payments')->send();
            return;
        }

        $this->dialog()
            ->question('Delete Payments?', 'Delete ' . count($this->selected) . ' selected payments?')
            ->confirm('Delete All', 'executeBulkDelete')
            ->cancel()
            ->send();
    }

    public function executeBulkDelete(): void
    {
        $deletedCount = Payment::whereIn('id', $this->selected)->count();
        Payment::whereIn('id', $this->selected)->delete();

        $this->selected = [];
        $this->dispatch('payments-updated');
        $this->toast()->success('Success', "Deleted {$deletedCount} payments")->send();
    }

    public function exportSelected(): void
    {
        if (empty($this->selected)) {
            $this->toast()->warning('Warning', 'Please select payments')->send();
            return;
        }

        $this->toast()->info('Export Started', 'Exporting ' . count($this->selected) . ' payments')->send();
    }

    // Auto-reset pagination
    public function updatedSearch(): void
    {
        $this->resetPage();
    }
    public function updatedDateRange(): void
    {
        $this->resetPage();
    }
}