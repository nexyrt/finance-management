<?php

namespace App\Livewire\Accounts\Tables;

use App\Models\Payment;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Renderless;
use Livewire\Attributes\On;
use TallStackUi\Traits\Interactions;

class PaymentsTable extends Component
{
    use WithPagination, Interactions;

    // Account selection
    public $selectedAccountId;

    // Internal filters (self-contained)
    public string $search = '';
    public array $dateRange = [];

    // Table props
    public array $sort = [
        'column' => 'payment_date',
        'direction' => 'desc',
    ];
    public array $selected = [];
    public ?int $quantity = 10;

    // Static headers
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

    // Listen to account changes from parent
    #[On('account-selected')]
    public function handleAccountChange($accountId): void
    {
        $this->selectedAccountId = $accountId;
        $this->clearFilters();
        $this->resetPage();

        // Dispatch Alpine reinit
        $this->dispatch('reinit-alpine');
    }

    // Data loading
    #[Computed]
    public function rows()
    {
        if (!$this->selectedAccountId) {
            return collect();
        }

        $query = Payment::with(['invoice.client', 'bankAccount'])
            ->where('bank_account_id', $this->selectedAccountId)
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('reference_number', 'like', "%{$this->search}%")
                        ->orWhereHas('invoice', fn($q) => $q->where('invoice_number', 'like', "%{$this->search}%"))
                        ->orWhereHas('invoice.client', fn($q) => $q->where('name', 'like', "%{$this->search}%"));
                });
            })
            ->when(!empty($this->dateRange) && count($this->dateRange) >= 2, function ($q) {
                $q->whereBetween('payment_date', $this->dateRange);
            });

        return $query->orderBy(...array_values($this->sort))
            ->paginate($this->quantity)
            ->withQueryString();
    }

    // Filter management
    public function clearFilters(): void
    {
        $this->search = '';
        $this->dateRange = [];
        $this->resetPage();

        $this->toast()
            ->info('Filters Cleared', 'All payment filters reset')
            ->send();
    }

    // Actions
    public function addPayment(): void
    {
        if (!$this->selectedAccountId) {
            $this->toast()->warning('Warning', 'No account selected')->send();
            return;
        }
        $this->dispatch('open-payment-modal', accountId: $this->selectedAccountId);
    }

    public function deletePayment($paymentId): void
    {
        $this->dispatch('delete-payment', paymentId: $paymentId);
    }

    // Bulk operations
    #[Renderless]
    public function confirmBulkDelete(): void
    {
        if (empty($this->selected)) {
            $this->toast()->warning('Warning', 'Please select payments to delete')->send();
            return;
        }

        $count = count($this->selected);
        $selectedPayments = Payment::whereIn('id', $this->selected)->get();
        $totalAmount = $selectedPayments->sum('amount');

        $message = "Delete <strong>{$count} payments</strong>?<br><br>";
        $message .= "<div class='bg-zinc-50 dark:bg-dark-700 rounded-lg p-4 text-center'>";
        $message .= "<div class='text-sm text-dark-600'>Total Amount</div>";
        $message .= "<div class='font-bold text-lg'>Rp " . number_format($totalAmount, 0, ',', '.') . "</div>";
        $message .= "</div>";

        $this->dialog()
            ->question('Bulk Delete Payments?', $message)
            ->confirm('Delete All', 'executeBulkDelete')
            ->cancel('Cancel')
            ->send();
    }

    public function executeBulkDelete(): void
    {
        try {
            $deletedCount = Payment::whereIn('id', $this->selected)->count();
            Payment::whereIn('id', $this->selected)->delete();

            $this->selected = [];
            $this->dispatch('payments-updated');

            $this->toast()
                ->success('Success!', "Deleted {$deletedCount} payments.")
                ->send();

        } catch (\Exception $e) {
            $this->toast()
                ->error('Failed!', 'Error occurred while deleting payments.')
                ->send();
        }
    }

    public function exportSelected(): void
    {
        if (empty($this->selected)) {
            $this->toast()->warning('Warning', 'Please select payments to export')->send();
            return;
        }

        $count = count($this->selected);
        $this->toast()
            ->info('Export Started', "Exporting {$count} payments...")
            ->send();
    }

    // Auto-reset pagination on filter changes
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedDateRange(): void
    {
        $this->resetPage();
    }
}