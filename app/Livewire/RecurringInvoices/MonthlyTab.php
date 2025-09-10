<?php

namespace App\Livewire\RecurringInvoices;

use App\Models\RecurringInvoice;
use App\Models\RecurringTemplate;
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

    public function generateInvoices(): void
    {
        $templates = RecurringTemplate::where('status', 'active')
            ->where('next_generation_date', '<=', now())
            ->get();

        $generated = 0;
        foreach ($templates as $template) {
            if ($this->shouldGenerateForTemplate($template)) {
                $this->createInvoiceFromTemplate($template);
                $generated++;
            }
        }

        if ($generated > 0) {
            $this->toast()->success('Success', "$generated invoices generated successfully")->send();
            $this->dispatch('$refresh');
        } else {
            $this->toast()->info('Info', 'No invoices to generate at this time')->send();
        }
    }

    private function shouldGenerateForTemplate(RecurringTemplate $template): bool
    {
        return !RecurringInvoice::where('template_id', $template->id)
            ->whereYear('scheduled_date', $this->currentYear)
            ->whereMonth('scheduled_date', $this->currentMonth)
            ->exists();
    }

    private function createInvoiceFromTemplate(RecurringTemplate $template): void
    {
        $scheduledDate = now()->setYear($this->currentYear)->setMonth($this->currentMonth)->startOfMonth();

        RecurringInvoice::create([
            'template_id' => $template->id,
            'client_id' => $template->client_id,
            'scheduled_date' => $scheduledDate,
            'invoice_data' => $template->invoice_template,
            'status' => 'draft',
        ]);
    }

    public function publishInvoice($invoiceId): void
    {
        $invoice = RecurringInvoice::find($invoiceId);

        if (!$invoice || $invoice->status === 'published') {
            $this->toast()->error('Error', 'Invoice cannot be published')->send();
            return;
        }

        try {
            $publishedInvoice = $invoice->publish();
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