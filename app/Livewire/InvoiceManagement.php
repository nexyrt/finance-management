<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Client;
use App\Models\Service;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;

class InvoiceManagement extends Component
{
    use WithPagination;

    // Invoice Form Properties
    public $invoice_number = '';
    public $billed_to_id = '';
    public $issue_date = '';
    public $due_date = '';
    public $status = 'draft';
    public $total_amount = 0;

    // Invoice Items
    public $invoiceItems = [];
    public $newItem = [
        'client_id' => '',
        'service_name' => '',
        'amount' => 0
    ];

    // Payment Form Properties
    public $payment_amount = 0;
    public $payment_date = '';
    public $payment_method = 'bank_transfer';
    public $bank_account_id = '';
    public $reference_number = '';
    public $selected_invoice_id = '';

    // Modal State
    public $showAddInvoiceModal = false;
    public $showEditInvoiceModal = false;
    public $showDetailModal = false;
    public $showPaymentModal = false;
    public $showDeleteModal = false;
    public $showBulkActionModal = false;
    public $showPreviewModal = false;

    // Search and Filter
    public $search = '';
    public $filterStatus = '';
    public $filterClient = '';
    public $filterDateRange = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    // Bulk Operations
    public $selectedInvoices = [];
    public $bulkAction = '';

    // Edit State
    public $editingInvoice = null;
    public $invoiceToDelete = null;
    public $invoiceDetail = null;
    public $paymentInvoice = null;

    // Auto-calculation
    public $autoCalculateTotal = true;

    protected function rules()
    {
        $invoiceId = $this->editingInvoice ? $this->editingInvoice->id : null;
        
        return [
            'invoice_number' => 'required|string|max:50|unique:invoices,invoice_number,' . $invoiceId,
            'billed_to_id' => 'required|exists:clients,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'status' => 'required|in:draft,sent,paid,partially_paid,overdue',
            'total_amount' => 'required|numeric|min:0',
            'invoiceItems' => 'required|array|min:1',
            'invoiceItems.*.client_id' => 'required|exists:clients,id',
            'invoiceItems.*.service_name' => 'required|string|max:255',
            'invoiceItems.*.amount' => 'required|numeric|min:0.01',
        ];
    }

    protected $paymentRules = [
        'payment_amount' => 'required|numeric|min:0.01',
        'payment_date' => 'required|date|before_or_equal:today',
        'payment_method' => 'required|in:cash,bank_transfer',
        'bank_account_id' => 'required_if:payment_method,bank_transfer|exists:bank_accounts,id',
        'reference_number' => 'nullable|string|max:100',
        'selected_invoice_id' => 'required|exists:invoices,id',
    ];

    protected $messages = [
        'invoice_number.required' => 'Nomor invoice wajib diisi.',
        'invoice_number.unique' => 'Nomor invoice sudah digunakan.',
        'billed_to_id.required' => 'Klien wajib dipilih.',
        'due_date.after_or_equal' => 'Tanggal jatuh tempo harus setelah atau sama dengan tanggal invoice.',
        'invoiceItems.required' => 'Minimal harus ada satu item.',
        'invoiceItems.min' => 'Minimal harus ada satu item.',
        'payment_amount.required' => 'Jumlah pembayaran wajib diisi.',
        'payment_amount.min' => 'Jumlah pembayaran minimal 0.01.',
        'bank_account_id.required_if' => 'Akun bank wajib dipilih untuk transfer bank.',
    ];

    public function mount()
    {
        $this->issue_date = Carbon::now()->format('Y-m-d');
        $this->due_date = Carbon::now()->addDays(30)->format('Y-m-d');
        $this->payment_date = Carbon::now()->format('Y-m-d');
        $this->generateInvoiceNumber();
        $this->addInvoiceItem();
    }

    // Computed Properties
    #[Computed]
    public function totalInvoices()
    {
        return Invoice::count();
    }

    #[Computed]
    public function totalRevenue()
    {
        return Invoice::where('status', 'paid')->sum('total_amount');
    }

    #[Computed]
    public function pendingAmount()
    {
        return Invoice::whereIn('status', ['sent', 'partially_paid', 'overdue'])->sum('total_amount');
    }

    #[Computed]
    public function overdueInvoices()
    {
        return Invoice::where('status', 'overdue')->count();
    }

    #[Computed]
    public function recentInvoices()
    {
        return Invoice::with('client')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'client_name' => $invoice->client->name,
                    'total_amount' => $invoice->total_amount,
                    'status' => $invoice->status,
                    'due_date' => $invoice->due_date,
                    'formatted_date' => Carbon::parse($invoice->created_at)->diffForHumans(),
                    'is_overdue' => Carbon::parse($invoice->due_date)->isPast() && $invoice->status !== 'paid',
                ];
            });
    }

    #[Computed]
    public function invoices()
    {
        $query = Invoice::with(['client', 'payments']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('invoice_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('client', function ($clientQuery) {
                        $clientQuery->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterClient) {
            $query->where('billed_to_id', $this->filterClient);
        }

        if ($this->filterDateRange) {
            $this->applyDateRangeFilter($query);
        }

        return $query->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(12);
    }

    #[Computed]
    public function availableClients()
    {
        return Client::where('status', 'Active')
            ->select('id', 'name', 'type')
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function availableServices()
    {
        return Service::select('id', 'name', 'price', 'type')
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function availableBankAccounts()
    {
        return \App\Models\BankAccount::select('id', 'bank_name', 'account_number', 'account_name')
            ->orderBy('bank_name')
            ->get();
    }

    #[Computed]
    public function invoiceStats()
    {
        $stats = Invoice::selectRaw('
            status,
            COUNT(*) as count,
            SUM(total_amount) as total
        ')->groupBy('status')->get();

        $result = [
            'draft' => ['count' => 0, 'total' => 0],
            'sent' => ['count' => 0, 'total' => 0],
            'paid' => ['count' => 0, 'total' => 0],
            'partially_paid' => ['count' => 0, 'total' => 0],
            'overdue' => ['count' => 0, 'total' => 0],
        ];

        foreach ($stats as $stat) {
            $result[$stat->status] = [
                'count' => $stat->count,
                'total' => $stat->total
            ];
        }

        return $result;
    }

    // Modal Management
    public function openAddInvoiceModal()
    {
        $this->resetInvoiceForm();
        $this->generateInvoiceNumber();
        $this->addInvoiceItem();
        $this->showAddInvoiceModal = true;
    }

    public function openEditInvoiceModal($invoiceId)
    {
        $this->editingInvoice = Invoice::with(['items', 'client'])->findOrFail($invoiceId);
        $this->fillFormFromInvoice($this->editingInvoice);
        $this->showEditInvoiceModal = true;
    }

    public function openDetailModal($invoiceId)
    {
        $this->invoiceDetail = Invoice::with(['client', 'items', 'payments.bankAccount'])
            ->findOrFail($invoiceId);
        $this->showDetailModal = true;
    }

    public function openPaymentModal($invoiceId)
    {
        $this->paymentInvoice = Invoice::with('client')->findOrFail($invoiceId);
        $this->selected_invoice_id = $invoiceId;
        $this->payment_amount = $this->paymentInvoice->amount_remaining;
        $this->resetPaymentForm();
        $this->showPaymentModal = true;
    }

    public function openPreviewModal($invoiceId)
    {
        $this->invoiceDetail = Invoice::with(['client', 'items'])->findOrFail($invoiceId);
        $this->showPreviewModal = true;
    }

    public function confirmDeleteInvoice($invoiceId)
    {
        $this->invoiceToDelete = Invoice::findOrFail($invoiceId);
        $this->showDeleteModal = true;
    }

    // Invoice Operations
    public function saveInvoice()
    {
        $this->calculateTotal();
        $this->validate();

        try {
            DB::transaction(function () {
                $invoice = Invoice::create([
                    'invoice_number' => $this->invoice_number,
                    'billed_to_id' => $this->billed_to_id,
                    'total_amount' => $this->total_amount,
                    'issue_date' => $this->issue_date,
                    'due_date' => $this->due_date,
                    'status' => $this->status,
                ]);

                foreach ($this->invoiceItems as $item) {
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'client_id' => $item['client_id'],
                        'service_name' => $item['service_name'],
                        'amount' => $item['amount'],
                    ]);
                }
            });

            $this->dispatch('notify', type: 'success', message: 'Invoice berhasil dibuat!');
            $this->showAddInvoiceModal = false;
            $this->resetInvoiceForm();
            
            unset($this->totalInvoices, $this->invoices, $this->recentInvoices);
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Gagal membuat invoice: ' . $e->getMessage());
        }
    }

    public function updateInvoice()
    {
        $this->calculateTotal();
        $this->validate();

        try {
            DB::transaction(function () {
                $this->editingInvoice->update([
                    'invoice_number' => $this->invoice_number,
                    'billed_to_id' => $this->billed_to_id,
                    'total_amount' => $this->total_amount,
                    'issue_date' => $this->issue_date,
                    'due_date' => $this->due_date,
                    'status' => $this->status,
                ]);

                // Delete existing items
                $this->editingInvoice->items()->delete();

                // Create new items
                foreach ($this->invoiceItems as $item) {
                    InvoiceItem::create([
                        'invoice_id' => $this->editingInvoice->id,
                        'client_id' => $item['client_id'],
                        'service_name' => $item['service_name'],
                        'amount' => $item['amount'],
                    ]);
                }

                // Update invoice status if needed
                $this->editingInvoice->updateStatus();
            });

            $this->dispatch('notify', type: 'success', message: 'Invoice berhasil diperbarui!');
            $this->showEditInvoiceModal = false;
            $this->resetInvoiceForm();
            
            unset($this->invoices, $this->recentInvoices);
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Gagal memperbarui invoice: ' . $e->getMessage());
        }
    }

    public function deleteInvoice()
    {
        try {
            DB::transaction(function () {
                // Delete related payments and items
                $this->invoiceToDelete->payments()->delete();
                $this->invoiceToDelete->items()->delete();
                $this->invoiceToDelete->delete();
            });

            $this->dispatch('notify', type: 'success', message: 'Invoice berhasil dihapus!');
            $this->showDeleteModal = false;
            $this->invoiceToDelete = null;
            
            unset($this->totalInvoices, $this->invoices, $this->recentInvoices);
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Gagal menghapus invoice: ' . $e->getMessage());
        }
    }

    // Payment Operations
    public function savePayment()
    {
        $this->validate($this->paymentRules);

        try {
            DB::transaction(function () {
                $invoice = Invoice::findOrFail($this->selected_invoice_id);

                // Validate payment amount
                if ($this->payment_amount > $invoice->amount_remaining) {
                    throw new \Exception('Jumlah pembayaran melebihi sisa tagihan.');
                }

                // Create payment
                Payment::create([
                    'invoice_id' => $this->selected_invoice_id,
                    'bank_account_id' => $this->payment_method === 'bank_transfer' ? $this->bank_account_id : null,
                    'amount' => $this->payment_amount,
                    'payment_date' => $this->payment_date,
                    'payment_method' => $this->payment_method,
                    'reference_number' => $this->reference_number,
                ]);

                // Update bank account balance if bank transfer
                if ($this->payment_method === 'bank_transfer' && $this->bank_account_id) {
                    $bankAccount = \App\Models\BankAccount::findOrFail($this->bank_account_id);
                    $bankAccount->increment('current_balance', $this->payment_amount);

                    // Create bank transaction
                    \App\Models\BankTransaction::create([
                        'bank_account_id' => $this->bank_account_id,
                        'amount' => $this->payment_amount,
                        'transaction_date' => $this->payment_date,
                        'transaction_type' => 'credit',
                        'description' => 'Pembayaran Invoice ' . $invoice->invoice_number,
                        'reference_number' => $this->reference_number,
                    ]);
                }

                // Update invoice status
                $invoice->updateStatus();
            });

            $this->dispatch('notify', type: 'success', message: 'Pembayaran berhasil dicatat!');
            $this->showPaymentModal = false;
            $this->resetPaymentForm();
            
            unset($this->invoices, $this->recentInvoices, $this->totalRevenue, $this->pendingAmount);
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Gagal mencatat pembayaran: ' . $e->getMessage());
        }
    }

    // Invoice Item Management
    public function addInvoiceItem()
    {
        $this->invoiceItems[] = [
            'client_id' => $this->billed_to_id,
            'service_name' => '',
            'amount' => 0
        ];
    }

    public function removeInvoiceItem($index)
    {
        if (count($this->invoiceItems) > 1) {
            unset($this->invoiceItems[$index]);
            $this->invoiceItems = array_values($this->invoiceItems);
            $this->calculateTotal();
        }
    }

    public function selectService($index, $serviceId)
    {
        $service = $this->availableServices->find($serviceId);
        if ($service) {
            $this->invoiceItems[$index]['service_name'] = $service->name;
            $this->invoiceItems[$index]['amount'] = $service->price;
            $this->calculateTotal();
        }
    }

    public function calculateTotal()
    {
        if ($this->autoCalculateTotal) {
            $this->total_amount = collect($this->invoiceItems)->sum('amount');
        }
    }

    // Status Management
    public function updateInvoiceStatus($invoiceId, $status)
    {
        try {
            $invoice = Invoice::findOrFail($invoiceId);
            $invoice->update(['status' => $status]);
            $invoice->updateStatus(); // Re-validate status based on payments

            $this->dispatch('notify', type: 'success', message: 'Status invoice berhasil diperbarui!');
            unset($this->invoices, $this->recentInvoices);
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }

    public function sendInvoice($invoiceId)
    {
        $this->updateInvoiceStatus($invoiceId, 'sent');
        // TODO: Implement email sending logic
        $this->dispatch('notify', type: 'info', message: 'Invoice telah dikirim ke klien!');
    }

    // Bulk Operations
    public function openBulkActionModal()
    {
        if (empty($this->selectedInvoices)) {
            $this->dispatch('notify', type: 'warning', message: 'Pilih invoice terlebih dahulu!');
            return;
        }
        $this->showBulkActionModal = true;
    }

    public function processBulkAction()
    {
        if (empty($this->selectedInvoices) || empty($this->bulkAction)) {
            $this->dispatch('notify', type: 'warning', message: 'Pilih invoice dan aksi terlebih dahulu!');
            return;
        }

        try {
            DB::transaction(function () {
                switch ($this->bulkAction) {
                    case 'mark_sent':
                        Invoice::whereIn('id', $this->selectedInvoices)->update(['status' => 'sent']);
                        $message = 'Invoice berhasil ditandai sebagai terkirim!';
                        break;
                    case 'mark_overdue':
                        Invoice::whereIn('id', $this->selectedInvoices)->update(['status' => 'overdue']);
                        $message = 'Invoice berhasil ditandai sebagai overdue!';
                        break;
                    case 'delete':
                        $invoices = Invoice::whereIn('id', $this->selectedInvoices)->get();
                        foreach ($invoices as $invoice) {
                            $invoice->payments()->delete();
                            $invoice->items()->delete();
                            $invoice->delete();
                        }
                        $message = 'Invoice berhasil dihapus!';
                        break;
                    default:
                        throw new \Exception('Aksi tidak valid');
                }
            });

            $this->dispatch('notify', type: 'success', message: $message);
            $this->showBulkActionModal = false;
            $this->selectedInvoices = [];
            $this->bulkAction = '';
            
            unset($this->invoices, $this->totalInvoices, $this->recentInvoices);
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Gagal memproses aksi: ' . $e->getMessage());
        }
    }

    // Helper Methods
    private function generateInvoiceNumber()
    {
        $lastInvoice = Invoice::orderBy('id', 'desc')->first();
        $nextNumber = $lastInvoice ? (intval(substr($lastInvoice->invoice_number, -4)) + 1) : 1;
        $this->invoice_number = 'INV-' . Carbon::now()->format('Y') . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    private function resetInvoiceForm()
    {
        $this->invoice_number = '';
        $this->billed_to_id = '';
        $this->total_amount = 0;
        $this->issue_date = Carbon::now()->format('Y-m-d');
        $this->due_date = Carbon::now()->addDays(30)->format('Y-m-d');
        $this->status = 'draft';
        $this->invoiceItems = [];
        $this->editingInvoice = null;
        $this->resetErrorBag();
    }

    private function resetPaymentForm()
    {
        $this->payment_amount = 0;
        $this->payment_date = Carbon::now()->format('Y-m-d');
        $this->payment_method = 'bank_transfer';
        $this->bank_account_id = '';
        $this->reference_number = '';
        $this->resetErrorBag(['payment_amount', 'payment_date', 'payment_method', 'bank_account_id', 'reference_number']);
    }

    private function fillFormFromInvoice($invoice)
    {
        $this->invoice_number = $invoice->invoice_number;
        $this->billed_to_id = $invoice->billed_to_id;
        $this->total_amount = $invoice->total_amount;
        $this->issue_date = $invoice->issue_date->format('Y-m-d');
        $this->due_date = $invoice->due_date->format('Y-m-d');
        $this->status = $invoice->status;

        $this->invoiceItems = $invoice->items->map(function ($item) {
            return [
                'client_id' => $item->client_id,
                'service_name' => $item->service_name,
                'amount' => $item->amount,
            ];
        })->toArray();
    }

    private function applyDateRangeFilter($query)
    {
        $dates = explode(' to ', $this->filterDateRange);
        
        if (count($dates) === 2) {
            $query->whereDate('issue_date', '>=', Carbon::parse(trim($dates[0])))
                  ->whereDate('issue_date', '<=', Carbon::parse(trim($dates[1])));
        } elseif (count($dates) === 1 && !empty(trim($dates[0]))) {
            $query->whereDate('issue_date', '=', Carbon::parse(trim($dates[0])));
        }
    }

    // Utility Methods
    public function formatCurrency($amount)
    {
        return 'Rp ' . number_format((float)$amount, 0, ',', '.');
    }

    public function getStatusClass($status)
    {
        $classes = [
            'draft' => 'bg-gray-100 dark:bg-gray-900/30 text-gray-700 dark:text-gray-400',
            'sent' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
            'paid' => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400',
            'partially_paid' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400',
            'overdue' => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400',
        ];

        return $classes[$status] ?? $classes['draft'];
    }

    public function getStatusLabel($status)
    {
        $labels = [
            'draft' => 'Draft',
            'sent' => 'Terkirim',
            'paid' => 'Lunas',
            'partially_paid' => 'Dibayar Sebagian',
            'overdue' => 'Overdue',
        ];

        return $labels[$status] ?? 'Unknown';
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        
        unset($this->invoices);
    }

    // Add new method for badge colors
    public function getStatusColor($status)
    {
        $colors = [
            'draft' => 'zinc',
            'sent' => 'blue',
            'paid' => 'green',
            'partially_paid' => 'amber',
            'overdue' => 'red',
        ];

        return $colors[$status] ?? 'zinc';
    }

    public function toggleSelectAll()
    {
        if (count($this->selectedInvoices) === $this->invoices->count()) {
            $this->selectedInvoices = [];
        } else {
            $this->selectedInvoices = $this->invoices->pluck('id')->toArray();
        }
    }

    // Livewire lifecycle hooks
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterClient()
    {
        $this->resetPage();
    }

    public function updatingFilterDateRange()
    {
        $this->resetPage();
    }

    public function updatingBilledToId()
    {
        // Update all invoice items to use the selected client
        foreach ($this->invoiceItems as $index => $item) {
            $this->invoiceItems[$index]['client_id'] = $this->billed_to_id;
        }
    }

    public function updatedInvoiceItems()
    {
        $this->calculateTotal();
    }

    public function render()
    {
        return view('livewire.invoice-management');
    }
}