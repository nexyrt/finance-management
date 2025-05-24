<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\Service;
use App\Models\ServiceClient;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\BankAccount;
use Carbon\Carbon;
use Flux\Flux;

class TestingPage extends Component
{
    use WithPagination;

    // Properties untuk UI state
    public $activeTab = 'list';
    public $search = '';
    public $statusFilter = 'all';
    public $dateFrom = '';
    public $dateTo = '';
    public $filterDate = '';

    // Properties untuk form buat invoice
    public $newInvoice = [
        'invoice_number' => '',
        'billed_to_id' => '',
        'issue_date' => '',
        'due_date' => '',
        'payment_terms' => 'full',
        'installment_count' => 1,
    ];

    // Properties untuk tambah item
    public $selectedInvoiceId = '';
    public $selectedServiceClientIds = [];
    public $availableServiceClients = [];

    // Properties untuk detail invoice
    public $viewingInvoice = null;
    public $paymentForm = [
        'amount' => '',
        'payment_date' => '',
        'payment_method' => 'bank_transfer',
        'reference_number' => '',
        'bank_account_id' => '',
        'installment_number' => 1,
    ];

    // Properties untuk modal states menggunakan Flux
    public $showCreateModal = false;
    public $showViewModal = false;
    public $showPaymentModal = false;

    protected $rules = [
        'newInvoice.billed_to_id' => 'required|exists:clients,id',
        'newInvoice.issue_date' => 'required|date',
        'newInvoice.due_date' => 'required|date|after:newInvoice.issue_date',
        'newInvoice.payment_terms' => 'required|in:full,installment',
        'newInvoice.installment_count' => 'required|integer|min:1',
        'paymentForm.amount' => 'required|numeric|min:0.01',
        'paymentForm.payment_date' => 'required|date',
        'paymentForm.payment_method' => 'required|in:cash,bank_transfer,credit_card,check,other',
        'paymentForm.bank_account_id' => 'required|exists:bank_accounts,id',
    ];

    public function mount()
    {
        $this->newInvoice['issue_date'] = now()->format('Y-m-d');
        $this->newInvoice['due_date'] = now()->addDays(30)->format('Y-m-d');
        $this->paymentForm['payment_date'] = now()->format('Y-m-d');
    }

    public function updatedActiveTab()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedFilterDate()
    {
        // Parse date range from custom datepicker
        if ($this->filterDate && str_contains($this->filterDate, ' to ')) {
            [$this->dateFrom, $this->dateTo] = explode(' to ', $this->filterDate);
        } elseif ($this->filterDate) {
            $this->dateFrom = $this->filterDate;
            $this->dateTo = $this->filterDate;
        } else {
            $this->dateFrom = '';
            $this->dateTo = '';
        }
        $this->resetPage();
    }

    public function updatedSelectedInvoiceId()
    {
        $this->loadAvailableServiceClients();
    }

    // Generate unique invoice number
    private function generateInvoiceNumber()
    {
        $year = date('Y');
        $month = date('m');
        $lastInvoice = Invoice::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        
        $nextNumber = $lastInvoice ? (int)substr($lastInvoice->invoice_number, -4) + 1 : 1;
        
        return sprintf('INV-%s%s-%04d', $year, $month, $nextNumber);
    }

    // Buat Invoice Baru
    public function createInvoice()
    {
        $this->validate([
            'newInvoice.billed_to_id' => 'required|exists:clients,id',
            'newInvoice.issue_date' => 'required|date',
            'newInvoice.due_date' => 'required|date|after:newInvoice.issue_date',
            'newInvoice.payment_terms' => 'required|in:full,installment',
            'newInvoice.installment_count' => 'required|integer|min:1',
        ]);

        $this->newInvoice['invoice_number'] = $this->generateInvoiceNumber();
        $this->newInvoice['total_amount'] = 0;
        $this->newInvoice['status'] = 'draft';

        $invoice = Invoice::create($this->newInvoice);

        $this->reset('newInvoice', 'showCreateModal');
        $this->newInvoice['issue_date'] = now()->format('Y-m-d');
        $this->newInvoice['due_date'] = now()->addDays(30)->format('Y-m-d');
        $this->newInvoice['payment_terms'] = 'full';
        $this->newInvoice['installment_count'] = 1;

        // Tutup modal menggunakan Flux
        $this->modal('create-invoice')->close();

        session()->flash('success', 'Invoice berhasil dibuat!');
        
        // Pindah ke tab tambah item dengan invoice yang baru dibuat
        $this->selectedInvoiceId = $invoice->id;
        $this->activeTab = 'add-items';
        $this->loadAvailableServiceClients();
    }

    // Load service clients yang belum di-invoice
    public function loadAvailableServiceClients()
    {
        if (!$this->selectedInvoiceId) {
            $this->availableServiceClients = [];
            return;
        }

        $invoice = Invoice::find($this->selectedInvoiceId);
        if (!$invoice) {
            $this->availableServiceClients = [];
            return;
        }

        // Get service clients untuk client ini yang belum ada di invoice manapun
        $usedServiceClientIds = InvoiceItem::pluck('service_client_id')->toArray();
        
        $this->availableServiceClients = ServiceClient::with(['service', 'client'])
            ->where('client_id', $invoice->billed_to_id)
            ->whereNotIn('id', $usedServiceClientIds)
            ->get()
            ->toArray();
    }

    // Tambah item ke invoice
    public function addItemsToInvoice()
    {
        if (empty($this->selectedServiceClientIds)) {
            session()->flash('error', 'Pilih minimal satu item untuk ditambahkan.');
            return;
        }

        $invoice = Invoice::find($this->selectedInvoiceId);
        $totalAmount = 0;

        foreach ($this->selectedServiceClientIds as $serviceClientId) {
            $serviceClient = ServiceClient::find($serviceClientId);
            
            InvoiceItem::create([
                'invoice_id' => $this->selectedInvoiceId,
                'service_client_id' => $serviceClientId,
                'amount' => $serviceClient->amount,
            ]);

            $totalAmount += $serviceClient->amount;
        }

        // Update total amount invoice
        $invoice->total_amount += $totalAmount;
        $invoice->save();

        $this->reset('selectedServiceClientIds');
        $this->loadAvailableServiceClients();

        session()->flash('success', 'Item berhasil ditambahkan ke invoice!');
    }

    // Lihat detail invoice
    public function viewInvoice($invoiceId)
    {
        $this->viewingInvoice = Invoice::with([
            'client', 
            'items.serviceClient.service', 
            'payments.bankAccount'
        ])->find($invoiceId);
        
        $this->modal('view-invoice')->show();
    }

    // Tambah pembayaran
    public function addPayment()
    {
        $this->validate([
            'paymentForm.amount' => 'required|numeric|min:0.01',
            'paymentForm.payment_date' => 'required|date',
            'paymentForm.payment_method' => 'required|in:cash,bank_transfer,credit_card,check,other',
            'paymentForm.bank_account_id' => 'required|exists:bank_accounts,id',
        ]);

        // Validasi amount tidak melebihi sisa yang harus dibayar
        $remainingAmount = $this->viewingInvoice->amount_remaining;
        if ($this->paymentForm['amount'] > $remainingAmount) {
            session()->flash('error', 'Jumlah pembayaran melebihi sisa yang harus dibayar.');
            return;
        }

        Payment::create([
            'invoice_id' => $this->viewingInvoice->id,
            'bank_account_id' => $this->paymentForm['bank_account_id'],
            'amount' => $this->paymentForm['amount'],
            'payment_date' => $this->paymentForm['payment_date'],
            'payment_method' => $this->paymentForm['payment_method'],
            'reference_number' => $this->paymentForm['reference_number'],
            'installment_number' => $this->paymentForm['installment_number'],
        ]);

        // Refresh data invoice
        $this->viewingInvoice->refresh();

        $this->reset('paymentForm');
        $this->paymentForm['payment_date'] = now()->format('Y-m-d');
        $this->paymentForm['payment_method'] = 'bank_transfer';
        $this->paymentForm['installment_number'] = 1;

        // Tutup modal pembayaran
        $this->modal('add-payment')->close();

        session()->flash('success', 'Pembayaran berhasil ditambahkan!');
    }

    // Delete invoice
    public function deleteInvoice($invoiceId)
    {
        $invoice = Invoice::find($invoiceId);
        if ($invoice) {
            $invoice->delete();
            session()->flash('success', 'Invoice berhasil dihapus!');
        }
    }

    // Update status invoice
    public function updateInvoiceStatus($invoiceId, $status)
    {
        $invoice = Invoice::find($invoiceId);
        if ($invoice) {
            $invoice->status = $status;
            $invoice->save();
            session()->flash('success', 'Status invoice berhasil diupdate!');
        }
    }

    // Duplicate invoice
    public function duplicateInvoice($invoiceId)
    {
        $originalInvoice = Invoice::with('items')->find($invoiceId);
        if (!$originalInvoice) return;

        $newInvoice = $originalInvoice->replicate();
        $newInvoice->invoice_number = $this->generateInvoiceNumber();
        $newInvoice->status = 'draft';
        $newInvoice->issue_date = now()->format('Y-m-d');
        $newInvoice->due_date = now()->addDays(30)->format('Y-m-d');
        $newInvoice->save();

        // Duplicate items
        foreach ($originalInvoice->items as $item) {
            $newItem = $item->replicate();
            $newItem->invoice_id = $newInvoice->id;
            $newItem->save();
        }

        session()->flash('success', 'Invoice berhasil diduplikasi!');
    }

    // Get invoice statistics
    public function getInvoiceStats()
    {
        return [
            'total' => Invoice::count(),
            'pending' => Invoice::whereIn('status', ['draft', 'sent'])->count(),
            'paid' => Invoice::where('status', 'paid')->count(),
            'overdue' => Invoice::where('status', 'overdue')->count(),
        ];
    }

    // Get clients for dropdown - format untuk custom select
    public function getClients()
    {
        return Client::orderBy('name')->get()->map(function ($client) {
            return [
                'value' => $client->id,
                'label' => $client->name . ' (' . ucfirst($client->type) . ')'
            ];
        })->toArray();
    }

    // Get bank accounts for dropdown - format untuk custom select
    public function getBankAccounts()
    {
        return BankAccount::orderBy('account_name')->get()->map(function ($account) {
            return [
                'value' => $account->id,
                'label' => $account->account_name . ' - ' . $account->bank_name
            ];
        })->toArray();
    }

    // Get status options untuk dropdown
    public function getStatusOptions()
    {
        return [
            ['value' => 'all', 'label' => 'Semua Status'],
            ['value' => 'pending', 'label' => 'Pending'],
            ['value' => 'paid', 'label' => 'Paid'],
            ['value' => 'overdue', 'label' => 'Overdue'],
        ];
    }

    // Get payment method options
    public function getPaymentMethodOptions()
    {
        return [
            ['value' => 'cash', 'label' => 'Cash'],
            ['value' => 'bank_transfer', 'label' => 'Bank Transfer'],
            ['value' => 'credit_card', 'label' => 'Credit Card'],
            ['value' => 'check', 'label' => 'Check'],
            ['value' => 'other', 'label' => 'Other'],
        ];
    }

    // Get invoices with filters
    public function getInvoices()
    {
        $query = Invoice::with(['client', 'payments'])
            ->orderBy('created_at', 'desc');

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('invoice_number', 'like', '%' . $this->search . '%')
                  ->orWhereHas('client', function ($clientQuery) {
                      $clientQuery->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Apply status filter
        if ($this->statusFilter !== 'all') {
            if ($this->statusFilter === 'pending') {
                $query->whereIn('status', ['draft', 'sent']);
            } else {
                $query->where('status', $this->statusFilter);
            }
        }

        // Apply date filter
        if ($this->dateFrom) {
            $query->where('issue_date', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->where('issue_date', '<=', $this->dateTo);
        }

        return $query->paginate(10);
    }

    // Get invoices untuk dropdown (hanya draft)
    public function getDraftInvoices()
    {
        return Invoice::with('client')
            ->where('status', 'draft')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($invoice) {
                return [
                    'value' => $invoice->id,
                    'label' => $invoice->invoice_number . ' - ' . $invoice->client->name
                ];
            })->toArray();
    }

    // Format currency
    public function formatCurrency($amount)
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    // Format date
    public function formatDate($date)
    {
        return Carbon::parse($date)->format('d M Y');
    }

    // Get status badge class
    public function getStatusBadgeClass($status)
    {
        return match($status) {
            'paid' => 'bg-green-500/10 text-green-400 border-green-500/20',
            'partially_paid' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
            'overdue' => 'bg-red-500/10 text-red-400 border-red-500/20',
            'sent' => 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20',
            default => 'bg-gray-500/10 text-gray-400 border-gray-500/20'
        };
    }

    // Get status label
    public function getStatusLabel($status)
    {
        return match($status) {
            'draft' => 'Draft',
            'sent' => 'Terkirim',
            'paid' => 'Lunas',
            'partially_paid' => 'Sebagian',
            'overdue' => 'Terlambat',
            default => ucfirst($status)
        };
    }

    // Method untuk open modal dengan Flux
    public function openCreateModal()
    {
        $this->modal('create-invoice')->show();
    }

    public function openPaymentModal()
    {
        $this->modal('add-payment')->show();
    }

    public function render()
    {
        return view('livewire.testing-page', [
            'invoices' => $this->getInvoices(),
            'stats' => $this->getInvoiceStats(),
            'clients' => $this->getClients(),
            'bankAccounts' => $this->getBankAccounts(),
            'statusOptions' => $this->getStatusOptions(),
            'paymentMethodOptions' => $this->getPaymentMethodOptions(),
            'draftInvoices' => $this->getDraftInvoices(),
        ]);
    }
}
