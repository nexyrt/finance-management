<?php

namespace App\Livewire\RecurringInvoices;

use App\Models\RecurringInvoice;
use App\Models\RecurringTemplate;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class MonthlyTab extends Component
{
    use WithPagination, Interactions;

    public int $currentMonth;
    public int $currentYear;
    public ?int $selectedTemplate = null;
    public string $statusFilter = 'all';
    public array $selected = [];

    // Generate modal
    public bool $generateModal = false;
    public ?string $generateIssueDate = null;
    public ?string $generateDueDate = null;

    // Publish modal
    public bool $publishModal = false;
    public ?int $publishingInvoiceId = null;
    public ?string $publishIssueDate = null;
    public ?string $publishDueDate = null;

    protected $listeners = [
        'invoice-created' => '$refresh',
        'invoice-updated' => '$refresh',
        'invoice-deleted' => '$refresh',
    ];

    public function mount(): void
    {
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;
    }

    #[Computed]
    public function stats(): array
    {
        $invoices = $this->getFilteredInvoices()->get();

        $totalRevenue = $invoices->sum(function ($invoice) {
            return $invoice->invoice_data['total_amount'] ?? 0;
        });

        $totalCogs = $invoices->sum(function ($invoice) {
            return collect($invoice->invoice_data['items'] ?? [])->sum('cogs_amount');
        });

        $totalProfit = $totalRevenue - $totalCogs;
        $profitMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;

        // Outstanding profit (from unpublished invoices)
        $draftInvoices = $invoices->where('status', 'draft');
        $draftRevenue = $draftInvoices->sum(function ($invoice) {
            return $invoice->invoice_data['total_amount'] ?? 0;
        });
        $draftCogs = $draftInvoices->sum(function ($invoice) {
            return collect($invoice->invoice_data['items'] ?? [])->sum('cogs_amount');
        });
        $outstandingProfit = $draftRevenue - $draftCogs;
        $paidProfit = $totalProfit - $outstandingProfit;

        return [
            'total_revenue' => $totalRevenue,
            'total_cogs' => $totalCogs,
            'total_profit' => $totalProfit,
            'profit_margin' => $profitMargin,
            'outstanding_profit' => $outstandingProfit,
            'paid_profit' => $paidProfit,
        ];
    }

    #[Computed]
    public function invoices()
    {
        return $this->getFilteredInvoices()->paginate(12)->withQueryString();
    }

    #[Computed]
    public function templates()
    {
        return RecurringTemplate::where('status', 'active')
            ->with('client')
            ->get()
            ->map(fn($template) => [
                'label' => $template->client->name . ' - ' . $template->template_name,
                'value' => $template->id
            ])
            ->toArray();
    }

    #[Computed]
    public function monthOptions(): array
    {
        return collect(range(1, 12))->map(fn($month) => [
            'label' => now()->month($month)->format('F'),
            'value' => $month
        ])->toArray();
    }

    #[Computed]
    public function yearOptions(): array
    {
        $currentYear = now()->year;
        return collect(range($currentYear - 2, $currentYear + 2))->map(fn($year) => [
            'label' => (string) $year,
            'value' => $year
        ])->toArray();
    }

    private function getFilteredInvoices()
    {
        $query = RecurringInvoice::with(['client', 'template', 'publishedInvoice'])
            ->whereYear('scheduled_date', $this->currentYear)
            ->whereMonth('scheduled_date', $this->currentMonth);

        if ($this->selectedTemplate) {
            $query->where('template_id', $this->selectedTemplate);
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        return $query->orderBy('scheduled_date', 'desc');
    }

    public function openGenerateModal(): void
    {
        $this->generateIssueDate = now()->format('Y-m-d');
        $this->generateDueDate = now()->addDays(30)->format('Y-m-d');
        $this->generateModal = true;
    }

    public function generateInvoices(): void
    {
        $this->validate([
            'generateIssueDate' => 'required|date',
            'generateDueDate' => 'required|date|after_or_equal:generateIssueDate',
        ]);

        $scheduledDate = Carbon::create($this->currentYear, $this->currentMonth, 1);

        // Pre-filter: template yang start_date sebelum bulan dipilih dan end_date belum lewat.
        // Validasi presisi (apakah bulan ini tepat sesuai siklus frequency) dilakukan oleh isValidPeriodForGeneration().
        $templates = RecurringTemplate::where('status', 'active')
            ->where('start_date', '<', $scheduledDate)
            ->where('end_date', '>=', $scheduledDate)
            ->get();

        $generated = 0;
        foreach ($templates as $template) {
            if ($this->shouldGenerateForTemplate($template)) {
                $this->createInvoiceFromTemplate($template);
                $generated++;
            }
        }

        $this->generateModal = false;

        if ($generated > 0) {
            $this->toast()->success('Success', "$generated invoices generated successfully")->send();
            $this->dispatch('$refresh');
        } else {
            $this->toast()->info('Info', 'No invoices to generate at this time')->send();
        }
    }

    private function shouldGenerateForTemplate(RecurringTemplate $template): bool
    {
        $alreadyExists = RecurringInvoice::where('template_id', $template->id)
            ->whereYear('scheduled_date', $this->currentYear)
            ->whereMonth('scheduled_date', $this->currentMonth)
            ->exists();

        if ($alreadyExists) {
            return false;
        }

        return $template->isValidPeriodForGeneration($this->currentYear, $this->currentMonth);
    }

    private function createInvoiceFromTemplate(RecurringTemplate $template): void
    {
        $scheduledDate = Carbon::create($this->currentYear, $this->currentMonth, 1);

        RecurringInvoice::create([
            'template_id' => $template->id,
            'client_id' => $template->client_id,
            'scheduled_date' => $scheduledDate,
            'issue_date' => $this->generateIssueDate,
            'due_date' => $this->generateDueDate,
            'invoice_data' => $template->invoice_template,
            'status' => 'draft',
        ]);
    }

    public function openPublishModal(int $invoiceId): void
    {
        $invoice = RecurringInvoice::find($invoiceId);
        if (!$invoice || $invoice->status === 'published') {
            $this->toast()->error('Error', 'Invoice cannot be published')->send();
            return;
        }

        $this->publishingInvoiceId = $invoiceId;
        $this->publishIssueDate = $invoice->issue_date?->format('Y-m-d') ?? $invoice->scheduled_date->format('Y-m-d');
        $this->publishDueDate = $invoice->due_date?->format('Y-m-d') ?? $invoice->scheduled_date->copy()->addDays(30)->format('Y-m-d');
        $this->publishModal = true;
    }

    public function publishInvoice(): void
    {
        $this->validate([
            'publishIssueDate' => 'required|date',
            'publishDueDate' => 'required|date|after_or_equal:publishIssueDate',
        ]);

        $invoice = RecurringInvoice::find($this->publishingInvoiceId);

        if (!$invoice || $invoice->status === 'published') {
            $this->toast()->error('Error', 'Invoice cannot be published')->send();
            $this->publishModal = false;
            return;
        }

        try {
            $invoice->update([
                'issue_date' => $this->publishIssueDate,
                'due_date' => $this->publishDueDate,
            ]);

            $publishedInvoice = $invoice->publish();
            $this->publishModal = false;
            $this->toast()->success('Success', "Invoice published as #{$publishedInvoice->invoice_number}")->send();
            $this->dispatch('$refresh');
        } catch (\Exception $e) {
            $this->toast()->error('Error', $e->getMessage())->send();
        }
    }

    public function bulkDelete(): void
    {
        if (empty($this->selected)) {
            $this->toast()->warning('Warning', 'Please select invoices to delete')->send();
            return;
        }

        $this->toast()
            ->question('Delete Selected?', count($this->selected) . ' invoices will be deleted permanently')
            ->confirm('Delete', 'confirmBulkDelete', 'Invoices deleted successfully')
            ->cancel('Cancel')
            ->send();
    }

    public function confirmBulkDelete(): void
    {
        $deleted = 0;
        $invoices = RecurringInvoice::whereIn('id', $this->selected)->get();

        foreach ($invoices as $invoice) {
            try {
                $invoice->delete();
                $deleted++;
            } catch (\Exception $e) {
                \Log::error("Failed to delete invoice {$invoice->id}: " . $e->getMessage());
            }
        }

        $this->selected = [];

        if ($deleted > 0) {
            $this->toast()->success('Success', "$deleted invoices deleted successfully")->send();
            $this->dispatch('$refresh');
        } else {
            $this->toast()->error('Error', 'No invoices could be deleted')->send();
        }
    }

    public function bulkPublish(): void
    {
        if (empty($this->selected)) {
            $this->toast()->warning('Warning', 'Please select invoices to publish')->send();
            return;
        }

        $this->toast()
            ->question('Publish Selected?', count($this->selected) . ' invoices will be published')
            ->confirm('Publish', 'confirmBulkPublish', 'Invoices published successfully')
            ->cancel('Cancel')
            ->send();
    }

    public function confirmBulkPublish(): void
    {
        $published = 0;
        $invoices = RecurringInvoice::whereIn('id', $this->selected)
            ->where('status', 'draft')
            ->get();

        foreach ($invoices as $invoice) {
            try {
                $invoice->publish();
                $published++;
            } catch (\Exception $e) {
                \Log::error("Failed to publish invoice {$invoice->id}: " . $e->getMessage());
            }
        }

        $this->selected = [];

        if ($published > 0) {
            $this->toast()->success('Success', "$published invoices published successfully")->send();
            $this->dispatch('$refresh');
        } else {
            $this->toast()->error('Error', 'No invoices could be published')->send();
        }
    }

    public function createInvoice(): void
    {
        $this->dispatch('create-invoice');
    }

    public function editInvoice($invoiceId): void
    {
        $this->dispatch('edit-invoice', invoiceId: $invoiceId);
    }

    public function viewInvoice($invoiceId): void
    {
        $this->dispatch('view-invoice', invoiceId: $invoiceId);
    }

    public function deleteInvoice($invoiceId): void
    {
        $this->dispatch('delete-invoice', invoiceId: $invoiceId);
    }

    public function render()
    {
        return view('livewire.recurring-invoices.monthly-tab');
    }
}