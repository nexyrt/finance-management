<?php

namespace App\Livewire\Invoices;

use App\Models\Invoice;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

#[Lazy]
class Listing extends Component
{
    use Interactions, WithPagination;

    // Table properties
    public array $selected = [];

    public array $sort = ['column' => 'invoice_number', 'direction' => 'desc'];

    public $quantity = 25;

    // Filters
    public $statusFilter = null;

    public $clientFilter = null;

    public $selectedMonth = null;

    public $dateRange = [];

    public $search = '';

    // Send Invoice Modal
    public bool $sendModal = false;

    public ?int $pendingInvoiceId = null;

    public string $pendingInvoiceNumber = '';

    // Print Modal
    public bool $printModal = false;

    public $printInvoiceId = null;

    public $printTotalAmount = 0;

    public $printAmountPaid = 0;

    public $printType = 'full';

    public $dpAmount = null;

    public $printTemplate = 'kisantra-invoice';

    protected $listeners = [
        'invoice-created' => '$refresh',
        'invoice-updated' => '$refresh',
        'invoice-deleted' => '$refresh',
        'invoice-sent' => '$refresh',
        'payment-created' => '$refresh',
        'invoice-payment-updated' => '$refresh',
    ];

    public function placeholder(): View
    {
        return view('livewire.placeholders.invoices-skeleton');
    }

    public function mount(): void
    {
        $this->selectedMonth = now()->format('Y-m');
        $this->dateRange = [];
    }

    // -------------------------------------------------------------------------
    // Stats
    // -------------------------------------------------------------------------

    #[Computed]
    public function stats(): array
    {
        $filteredIds = $this->filteredIds();

        // Basic stats: count + total revenue (1 query)
        $basicStats = DB::table('invoices')
            ->whereIn('id', $filteredIds)
            ->selectRaw('COUNT(*) as invoice_count, COALESCE(SUM(total_amount), 0) as total_revenue')
            ->first();

        $invoiceCount = $basicStats->invoice_count;
        $totalRevenue = (int) $basicStats->total_revenue;

        // Item-level stats: tax deposits + COGS (1 query)
        $itemStats = DB::table('invoice_items')
            ->whereIn('invoice_id', $filteredIds)
            ->selectRaw('
                COALESCE(SUM(CASE WHEN is_tax_deposit = 1 THEN amount ELSE 0 END), 0) as total_tax_deposits,
                COALESCE(SUM(CASE WHEN is_tax_deposit = 0 THEN cogs_amount ELSE 0 END), 0) as total_cogs
            ')
            ->first();

        $totalTaxDeposits = (int) $itemStats->total_tax_deposits;
        $totalCogs = (int) $itemStats->total_cogs;
        $totalProfit = $totalRevenue - $totalTaxDeposits - $totalCogs;
        $profitMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;

        // Outstanding profit: pushed entirely to SQL (1 query, no PHP loop)
        $outstandingProfit = (int) DB::table('invoices')
            ->whereIn('invoices.id', $filteredIds)
            ->where('invoices.status', '!=', 'paid')
            ->leftJoin(DB::raw('(SELECT invoice_id, SUM(amount) as total_paid FROM payments GROUP BY invoice_id) as p'), 'invoices.id', '=', 'p.invoice_id')
            ->leftJoin(DB::raw('(SELECT invoice_id,
                SUM(CASE WHEN is_tax_deposit = 1 THEN amount ELSE 0 END) as tax_deposits,
                SUM(CASE WHEN is_tax_deposit = 0 THEN cogs_amount ELSE 0 END) as item_cogs
                FROM invoice_items GROUP BY invoice_id) as ii'), 'invoices.id', '=', 'ii.invoice_id')
            ->selectRaw('
                SUM(
                    CASE
                        WHEN (invoices.total_amount - COALESCE(ii.tax_deposits, 0) - COALESCE(ii.item_cogs, 0)) <= 0
                            THEN 0
                        WHEN COALESCE(p.total_paid, 0) <= (COALESCE(ii.item_cogs, 0) + COALESCE(ii.tax_deposits, 0))
                            THEN (invoices.total_amount - COALESCE(ii.tax_deposits, 0) - COALESCE(ii.item_cogs, 0))
                        ELSE GREATEST(0,
                            (invoices.total_amount - COALESCE(ii.tax_deposits, 0) - COALESCE(ii.item_cogs, 0))
                            - LEAST(
                                COALESCE(p.total_paid, 0) - (COALESCE(ii.item_cogs, 0) + COALESCE(ii.tax_deposits, 0)),
                                (invoices.total_amount - COALESCE(ii.tax_deposits, 0) - COALESCE(ii.item_cogs, 0))
                            )
                        )
                    END
                ) as outstanding_profit
            ')
            ->value('outstanding_profit') ?? 0;

        $paidProfit = $totalProfit - $outstandingProfit;

        // Payments this month (1 query, unfiltered)
        $paidThisMonth = DB::table('payments')
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');

        return [
            'total_revenue' => $totalRevenue,
            'total_cogs' => $totalCogs,
            'total_profit' => $totalProfit,
            'profit_margin' => $profitMargin,
            'outstanding_profit' => $outstandingProfit,
            'paid_profit' => $paidProfit,
            'paid_this_month' => (int) $paidThisMonth,
            'invoice_count' => $invoiceCount,
            'average_invoice_value' => $invoiceCount > 0 ? $totalRevenue / $invoiceCount : 0,
        ];
    }

    // -------------------------------------------------------------------------
    // Filters & Table
    // -------------------------------------------------------------------------

    #[Computed]
    public function activeFilters(): int
    {
        return (int) ($this->statusFilter !== null)
            + (int) ($this->clientFilter !== null)
            + (int) (! empty($this->dateRange))
            + (int) ($this->selectedMonth !== null);
    }

    #[Computed]
    public function headers(): array
    {
        return [
            ['index' => 'invoice_number', 'label' => __('invoice.invoice_number')],
            ['index' => 'client_name',    'label' => __('invoice.client')],
            ['index' => 'issue_date',     'label' => __('pages.date')],
            ['index' => 'due_date',       'label' => __('invoice.due_date')],
            ['index' => 'total_amount',   'label' => __('invoice.amount')],
            ['index' => 'status',         'label' => __('common.status')],
            ['index' => 'faktur',         'label' => __('invoice.faktur'),  'sortable' => false],
            ['index' => 'actions',        'label' => __('common.actions'),  'sortable' => false],
        ];
    }

    #[Computed]
    public function invoices(): LengthAwarePaginator
    {
        $query = Invoice::query()
            ->join('clients', 'invoices.billed_to_id', '=', 'clients.id')
            ->leftJoin(
                DB::raw('(SELECT invoice_id, COALESCE(SUM(amount), 0) as amount_paid FROM payments GROUP BY invoice_id) as p'),
                'invoices.id', '=', 'p.invoice_id'
            )
            ->select([
                'invoices.*',
                'clients.name as client_name',
                'clients.type as client_type',
                DB::raw('COALESCE(p.amount_paid, 0) as amount_paid'),
            ]);

        $this->applyFilters($query);

        match ($this->sort['column']) {
            'client_name' => $query->orderBy('clients.name', $this->sort['direction']),
            'invoice_number', 'issue_date', 'due_date', 'total_amount', 'status' => $query->orderBy('invoices.'.$this->sort['column'], $this->sort['direction']),
            default => $query->orderBy(...array_values($this->sort)),
        };

        return $query->paginate($this->quantity)->withQueryString();
    }

    // -------------------------------------------------------------------------
    // Filter update hooks
    // -------------------------------------------------------------------------

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
        unset($this->stats);
    }

    public function updatedClientFilter(): void
    {
        $this->resetPage();
        unset($this->stats);
    }

    public function updatedSelectedMonth(): void
    {
        $this->resetPage();
        unset($this->stats);
    }

    public function updatedDateRange(): void
    {
        $this->resetPage();
        unset($this->stats);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->fill([
            'statusFilter' => null,
            'clientFilter' => null,
            'selectedMonth' => null,
            'dateRange' => [],
        ]);
        $this->resetPage();
        unset($this->stats);
    }

    // -------------------------------------------------------------------------
    // Actions
    // -------------------------------------------------------------------------

    public function showInvoice(int $invoiceId): void
    {
        $this->dispatch('show-invoice', invoiceId: $invoiceId);
    }

    public function recordPayment(int $invoiceId): void
    {
        $this->dispatch('record-payment', invoiceId: $invoiceId);
    }

    public function deleteInvoice(int $invoiceId): void
    {
        $this->dispatch('delete-invoice', invoiceId: $invoiceId);
    }

    public function prepareSendInvoice(int $invoiceId): void
    {
        $invoice = Invoice::with('client')->find($invoiceId);

        if (! $invoice || $invoice->status !== 'draft') {
            $this->toast()->warning(__('common.warning'), __('invoice.only_draft_can_send'))->send();

            return;
        }

        $this->pendingInvoiceId = $invoiceId;
        $issueDate = \Carbon\Carbon::parse($invoice->issue_date);
        $this->pendingInvoiceNumber = $invoice->invoice_number
            ?? Invoice::generateInvoiceNumber($issueDate, $invoice->billed_to_id);
        $this->sendModal = true;
    }

    public function confirmSendInvoice(): void
    {
        $this->validate([
            'pendingInvoiceNumber' => 'required|string|max:100|unique:invoices,invoice_number,'.$this->pendingInvoiceId,
        ], [
            'pendingInvoiceNumber.required' => __('invoice.invoice_number_required'),
            'pendingInvoiceNumber.unique' => __('invoice.invoice_number_already_used'),
        ]);

        try {
            $invoice = Invoice::find($this->pendingInvoiceId);

            if (! $invoice || $invoice->status !== 'draft') {
                $this->toast()->warning(__('common.warning'), __('invoice.only_draft_can_send'))->send();
                $this->sendModal = false;

                return;
            }

            $invoice->update([
                'invoice_number' => $this->pendingInvoiceNumber,
                'status' => 'sent',
            ]);

            $this->sendModal = false;
            $this->toast()->success(__('common.success'), __('invoice.send_success', ['invoice_number' => $this->pendingInvoiceNumber]))->send();
            $this->dispatch('invoice-sent');
        } catch (\Exception $e) {
            $this->toast()->error(__('common.error'), __('invoice.send_error').': '.$e->getMessage())->send();
        }
    }

    public function rollbackTodraft(int $invoiceId): void
    {
        try {
            $invoice = Invoice::find($invoiceId);

            if (! $invoice || $invoice->status !== 'sent') {
                $this->toast()->warning(__('common.warning'), __('invoice.only_sent_rollback'))->send();

                return;
            }

            if (! Invoice::isInvoiceLatestInMonth($invoice)) {
                $this->toast()->warning(__('common.warning'), __('invoice.rollback_only_latest'))->send();

                return;
            }

            $invoice->update(['invoice_number' => null, 'status' => 'draft']);
            $this->toast()->success(__('common.success'), __('invoice.rollback_success'))->send();
            $this->dispatch('invoice-updated');
        } catch (\Exception $e) {
            $this->toast()->error(__('common.error'), __('invoice.rollback_error').': '.$e->getMessage())->send();
        }
    }

    // -------------------------------------------------------------------------
    // Print Modal
    // -------------------------------------------------------------------------

    public function openPrintModal(int $invoiceId, int $totalAmount, int $amountPaid = 0): void
    {
        $this->printInvoiceId = $invoiceId;
        $this->printTotalAmount = $totalAmount;
        $this->printAmountPaid = $amountPaid;
        $this->printType = 'full';
        $this->dpAmount = null;
        $this->printTemplate = 'kisantra-invoice';
        $this->printModal = true;
    }

    public function executePrint(): void
    {
        if (! $this->printInvoiceId) {
            $this->toast()->error(__('common.error'), __('invoice.not_found'))->send();

            return;
        }

        $previewUrl = route('invoice.preview', $this->printInvoiceId);
        $downloadUrl = route('invoice.download', $this->printInvoiceId);
        $templateParam = '?template='.$this->printTemplate;

        if ($this->printType === 'dp') {
            $dpParsed = $this->dpAmount ? (int) preg_replace('/[^0-9]/', '', $this->dpAmount) : 0;

            if ($dpParsed <= 0 || $dpParsed > $this->printTotalAmount) {
                $this->toast()->error(__('common.error'), __('invoice.invalid_dp_amount'))->send();

                return;
            }

            $previewUrl .= $templateParam.'&dp_amount='.$dpParsed;
            $downloadUrl .= $templateParam.'&dp_amount='.$dpParsed;
        } elseif ($this->printType === 'pelunasan') {
            $sisaPembayaran = $this->printTotalAmount - $this->printAmountPaid;

            if ($sisaPembayaran <= 0) {
                $this->toast()->error(__('common.error'), __('invoice.already_paid_full'))->send();

                return;
            }

            $previewUrl .= $templateParam.'&pelunasan_amount='.$sisaPembayaran;
            $downloadUrl .= $templateParam.'&pelunasan_amount='.$sisaPembayaran;
        } else {
            $previewUrl .= $templateParam;
            $downloadUrl .= $templateParam;
        }

        $this->dispatch('execute-print', ['previewUrl' => $previewUrl, 'downloadUrl' => $downloadUrl]);
        $this->printModal = false;
        $this->reset(['printInvoiceId', 'printTotalAmount', 'printAmountPaid', 'printType', 'dpAmount', 'printTemplate']);
    }

    // -------------------------------------------------------------------------
    // Bulk Actions
    // -------------------------------------------------------------------------

    public function bulkPrintInvoices(): void
    {
        if (empty($this->selected)) {
            $this->toast()->warning(__('common.warning'), __('invoice.select_for_print'))->send();

            return;
        }

        try {
            $invoices = Invoice::whereIn('id', $this->selected)->get(['id', 'invoice_number']);

            if ($invoices->isEmpty()) {
                $this->toast()->error(__('common.error'), __('invoice.not_found'))->send();

                return;
            }

            $this->dispatch('start-bulk-download', [
                'downloads' => $invoices->map(fn ($invoice) => [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'url' => route('invoice.download', $invoice->id),
                ])->toArray(),
                'delay' => 2000,
                'method' => 'iframe',
            ]);

            $this->selected = [];
            $this->toast()->success(__('common.success'), __('invoice.download_started', ['count' => $invoices->count()]))->send();
        } catch (\Exception $e) {
            $this->toast()->error(__('common.error'), __('invoice.download_error').': '.$e->getMessage())->send();
        }
    }

    public function bulkDelete(): void
    {
        if (empty($this->selected)) {
            $this->toast()->warning(__('common.warning'), __('invoice.select_for_delete'))->send();

            return;
        }

        try {
            DB::transaction(function () {
                \App\Models\InvoiceItem::whereIn('invoice_id', $this->selected)->delete();
                \App\Models\Invoice::whereIn('id', $this->selected)->delete();
            });

            $deletedCount = count($this->selected);
            $this->selected = [];
            $this->toast()->success(__('common.success'), __('invoice.bulk_delete_success', ['count' => $deletedCount]))->send();
            $this->dispatch('invoice-deleted');
        } catch (\Exception $e) {
            $this->toast()->error(__('common.error'), __('invoice.delete_error').': '.$e->getMessage())->send();
        }
    }

    // -------------------------------------------------------------------------
    // Export
    // -------------------------------------------------------------------------

    public function exportExcel()
    {
        return (new \App\Services\InvoiceExportService)->exportExcel($this->getFilters());
    }

    public function exportPdf()
    {
        $service = new \App\Services\InvoiceExportService;

        return response()->streamDownload(
            fn () => print $service->exportPdf($this->getFilters())->output(),
            'invoices-'.now()->format('Y-m-d').'.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function filteredIds()
    {
        return Invoice::query()
            ->join('clients', 'invoices.billed_to_id', '=', 'clients.id')
            ->select('invoices.id')
            ->when($this->statusFilter, fn ($q) => $q->where('invoices.status', $this->statusFilter))
            ->when($this->clientFilter, fn ($q) => $q->where('invoices.billed_to_id', $this->clientFilter))
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('invoices.invoice_number', 'like', '%'.$this->search.'%')
                        ->orWhere('clients.name', 'like', '%'.$this->search.'%');
                });
            })
            ->when(
                $this->dateRange && count($this->dateRange) >= 2 && $this->dateRange[0] && $this->dateRange[1],
                fn ($q) => $q->whereBetween('invoices.issue_date', [$this->dateRange[0], $this->dateRange[1]])
            )
            ->unless(
                $this->dateRange,
                fn ($q) => $q->when(
                    $this->selectedMonth,
                    fn ($query) => $query
                        ->whereYear('invoices.issue_date', substr($this->selectedMonth, 0, 4))
                        ->whereMonth('invoices.issue_date', substr($this->selectedMonth, 5, 2))
                )
            );
    }

    private function applyFilters($query): void
    {
        $query
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('invoices.invoice_number', 'like', '%'.$this->search.'%')
                        ->orWhere('clients.name', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->statusFilter, fn ($q) => $q->where('invoices.status', $this->statusFilter))
            ->when($this->clientFilter, fn ($q) => $q->where('invoices.billed_to_id', $this->clientFilter))
            ->when(
                $this->dateRange && count($this->dateRange) >= 2 && $this->dateRange[0] && $this->dateRange[1],
                fn ($q) => $q->whereBetween('invoices.issue_date', [$this->dateRange[0], $this->dateRange[1]])
            )
            ->unless(
                $this->dateRange,
                fn ($q) => $q->when(
                    $this->selectedMonth,
                    fn ($query) => $query
                        ->whereYear('invoices.issue_date', substr($this->selectedMonth, 0, 4))
                        ->whereMonth('invoices.issue_date', substr($this->selectedMonth, 5, 2))
                )
            );
    }

    private function getFilters(): array
    {
        return [
            'statusFilter' => $this->statusFilter,
            'clientFilter' => $this->clientFilter,
            'selectedMonth' => $this->selectedMonth,
            'dateRange' => $this->dateRange,
        ];
    }

    public function render()
    {
        return view('livewire.invoices.listing');
    }
}
