<?php

namespace App\Livewire\Invoices;

use App\Models\Invoice;
use App\Models\Client;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class Index extends Component
{
    use WithPagination, Interactions;

    protected $listeners = [
        'invoice-updated' => '$refresh',
        'payment-created' => '$refresh', 
        'invoice-payment-updated' => '$refresh',
        'confirm-bulk-delete' => 'openBulkDeleteModal',
        'invoice-created' => '$refresh',
    ];

    // Tab management
    public string $activeTab = 'invoices';
    
    // Table properties
    public array $selected = [];
    public array $sort = ['column' => 'invoice_number', 'direction' => 'desc'];
    public ?int $quantity = 10;
    
    // Filters
    public ?string $search = null;
    public ?string $statusFilter = null;
    public ?string $clientFilter = null;

    // Modal properties
    public bool $showBulkDeleteModal = false;

    /**
     * Trigger Create Invoice Modal
     */
    public function createInvoice(): void
    {
        $this->dispatch('create-invoice');
    }

    /**
     * Direct print via route (download PDF)
     * ✅ HAPUS ": void" untuk method yang return redirect
     */
    public function printInvoice(int $invoiceId)
    {
        return redirect()->route('invoices.print', $invoiceId);
    }

    /**
     * Preview PDF di tab baru
     */
    public function previewInvoice(int $invoiceId): void
    {
        $url = route('invoices.preview', $invoiceId);
        $this->dispatch('open-url', url: $url);
    }

    /**
     * Quick print (direct download tanpa modal)
     */
    public function quickPrint(int $invoiceId)
    {
        return redirect()->route('invoices.print', $invoiceId);
    }

    public function with(): array
    {
        // Calculate stats
        $stats = $this->calculateStats();
        
        return [
            'headers' => [
                ['index' => 'invoice_number', 'label' => 'No. Invoice'],
                ['index' => 'client_name', 'label' => 'Klien'],
                ['index' => 'issue_date', 'label' => 'Tanggal'],
                ['index' => 'due_date', 'label' => 'Jatuh Tempo'],
                ['index' => 'total_amount', 'label' => 'Jumlah'],
                ['index' => 'status', 'label' => 'Status'],
                ['index' => 'actions', 'label' => 'Aksi', 'sortable' => false],
            ],
            'rows' => $this->getInvoices(),
            'clients' => Client::select('id', 'name')->orderBy('name')->get(),
            'stats' => $stats,
        ];
    }

    private function getInvoices()
    {
        $query = Invoice::query()
            ->join('clients', 'invoices.billed_to_id', '=', 'clients.id')
            ->leftJoin('payments', 'invoices.id', '=', 'payments.invoice_id')
            ->select([
                'invoices.*',
                'clients.name as client_name',
                'clients.type as client_type',
                \DB::raw('COALESCE(SUM(payments.amount), 0) as amount_paid')
            ])
            ->groupBy([
                'invoices.id', 'invoices.invoice_number', 'invoices.billed_to_id', 
                'invoices.total_amount', 'invoices.issue_date', 'invoices.due_date', 
                'invoices.status', 'invoices.created_at', 'invoices.updated_at',
                'clients.name', 'clients.type'
            ]);

        // Apply filters
        if ($this->search) {
            $query->where(function($q) {
                $q->where('invoices.invoice_number', 'like', "%{$this->search}%")
                  ->orWhere('clients.name', 'like', "%{$this->search}%");
            });
        }

        if ($this->statusFilter) {
            $query->where('invoices.status', $this->statusFilter);
        }

        if ($this->clientFilter) {
            $query->where('invoices.billed_to_id', $this->clientFilter);
        }

        // Handle sorting
        if ($this->sort['column'] === 'client_name') {
            $query->orderBy('clients.name', $this->sort['direction']);
        } elseif (in_array($this->sort['column'], ['invoice_number', 'issue_date', 'due_date', 'total_amount', 'status'])) {
            $query->orderBy('invoices.' . $this->sort['column'], $this->sort['direction']);
        } else {
            $query->orderBy(...array_values($this->sort));
        }

        return $query->paginate($this->quantity)->withQueryString();
    }

    private function calculateStats(): array
    {
        $baseQuery = Invoice::query();
        
        return [
            'total_invoices' => $baseQuery->count(),
            'outstanding_amount' => $baseQuery->whereIn('status', ['sent', 'overdue', 'partially_paid'])
                ->sum('total_amount') - 
                \DB::table('payments')
                    ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
                    ->whereIn('invoices.status', ['sent', 'overdue', 'partially_paid'])
                    ->sum('payments.amount'),
            'paid_this_month' => \DB::table('payments')
                ->whereMonth('payment_date', now()->month)
                ->whereYear('payment_date', now()->year)
                ->sum('amount'),
            'overdue_count' => $baseQuery->where('status', 'overdue')->count(),
        ];
    }

    public function clearFilters(): void
    {
        $this->search = null;
        $this->statusFilter = null;
        $this->clientFilter = null;
        $this->resetPage();
    }

    public function openBulkDeleteModal(): void
    {
        if (empty($this->selected)) {
            $this->toast()->warning('Warning', 'Pilih minimal satu invoice untuk dihapus')->send();
            return;
        }
        
        $this->showBulkDeleteModal = true;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedClientFilter(): void
    {
        $this->resetPage();
    }

    public function sendInvoice(int $invoiceId): void
    {
        try {
            $invoice = Invoice::find($invoiceId);
            
            if (!$invoice) {
                $this->toast()->error('Error', 'Invoice tidak ditemukan')->send();
                return;
            }

            if ($invoice->status !== 'draft') {
                $this->toast()->warning('Warning', 'Hanya invoice draft yang bisa dikirim')->send();
                return;
            }

            $invoice->update(['status' => 'sent']);
            $this->toast()->success('Berhasil', "Invoice {$invoice->invoice_number} berhasil dikirim")->send();
            
        } catch (\Exception $e) {
            $this->toast()->error('Error', 'Gagal mengirim invoice: ' . $e->getMessage())->send();
        }
    }

    public function bulkDelete(): void
    {
        if (empty($this->selected)) {
            $this->toast()->warning('Warning', 'Pilih minimal satu invoice untuk dihapus')->send();
            return;
        }

        try {
            $invoices = Invoice::with(['payments'])->whereIn('id', $this->selected)->get();
            $deletedCount = 0;
            $skippedCount = 0;
            $deletedNumbers = [];

            foreach ($invoices as $invoice) {
                try {
                    if ($invoice->payments->count() > 0) {
                        $invoice->payments()->delete();
                    }
                    
                    $deletedNumbers[] = $invoice->invoice_number;
                    $invoice->delete();
                    $deletedCount++;
                    
                } catch (\Exception $e) {
                    $skippedCount++;
                    \Log::error("Failed to delete invoice {$invoice->invoice_number}: " . $e->getMessage());
                }
            }

            $this->selected = [];
            $this->showBulkDeleteModal = false;

            if ($deletedCount > 0) {
                $message = "Berhasil menghapus {$deletedCount} invoice";
                if ($skippedCount > 0) {
                    $message .= ", {$skippedCount} invoice gagal dihapus";
                }
                
                $this->dialog()
                    ->success('Bulk Delete Selesai', $message)
                    ->send();
            } else {
                $this->toast()->error('Error', 'Tidak ada invoice yang berhasil dihapus')->send();
            }

        } catch (\Exception $e) {
            $this->toast()->error('Error', 'Gagal melakukan bulk delete: ' . $e->getMessage())->send();
        }
    }

    public function bulkSend(): void
    {
        if (empty($this->selected)) {
            $this->toast()->warning('Warning', 'Pilih minimal satu invoice untuk dikirim')->send();
            return;
        }

        try {
            $invoices = Invoice::whereIn('id', $this->selected)
                ->where('status', 'draft')
                ->get();

            if ($invoices->isEmpty()) {
                $this->toast()->warning('Warning', 'Tidak ada invoice draft yang dipilih')->send();
                return;
            }

            $sentCount = 0;
            foreach ($invoices as $invoice) {
                try {
                    $invoice->update(['status' => 'sent']);
                    $sentCount++;
                } catch (\Exception $e) {
                    \Log::error("Failed to send invoice {$invoice->invoice_number}: " . $e->getMessage());
                }
            }

            $this->selected = [];

            if ($sentCount > 0) {
                $this->toast()
                    ->success('Berhasil', "Berhasil mengirim {$sentCount} invoice")
                    ->send();
            } else {
                $this->toast()->error('Error', 'Tidak ada invoice yang berhasil dikirim')->send();
            }

        } catch (\Exception $e) {
            $this->toast()->error('Error', 'Gagal mengirim invoice: ' . $e->getMessage())->send();
        }
    }

    public function bulkExport(): void
    {
        if (empty($this->selected)) {
            $this->toast()->warning('Warning', 'Pilih minimal satu invoice untuk diekspor')->send();
            return;
        }

        $this->toast()
            ->info('Info', 'Fitur export akan segera tersedia')
            ->send();
    }

    public function render()
    {
        return view('livewire.invoices.index', $this->with());
    }
}