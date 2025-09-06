<?php

namespace App\Livewire\RecurringInvoices;

use App\Models\RecurringTemplate;
use App\Models\RecurringInvoice;
use App\Models\Client;
use Livewire\Component;
use Livewire\Attributes\Computed;
use TallStackUi\Traits\Interactions;

class Index extends Component
{
    use Interactions;

    public string $activeTab = 'templates';
    public int $selectedMonth = 1;
    public int $selectedYear;
    public array $selected = [];
    
    protected $listeners = [
        'template-created' => '$refresh',
        'template-updated' => '$refresh',
        'invoice-published' => '$refresh',
    ];

    public function mount()
    {
        $this->selectedYear = now()->year;
        $this->selectedMonth = now()->month;
    }

    #[Computed]
    public function templates()
    {
        return RecurringTemplate::with('client')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    #[Computed]
    public function monthlyInvoices()
    {
        return RecurringInvoice::with(['client', 'template'])
            ->forMonth($this->selectedMonth, $this->selectedYear)
            ->orderBy('scheduled_date', 'asc')
            ->get();
    }

    #[Computed]
    public function monthlyStats()
    {
        $invoices = $this->monthlyInvoices;
        
        return [
            'total_count' => $invoices->count(),
            'draft_count' => $invoices->where('status', 'draft')->count(),
            'published_count' => $invoices->where('status', 'published')->count(),
            'total_amount' => $invoices->sum('total_amount'),
            'draft_amount' => $invoices->where('status', 'draft')->sum('total_amount'),
            'published_amount' => $invoices->where('status', 'published')->sum('total_amount'),
        ];
    }

    #[Computed]
    public function yearlyProjection()
    {
        $yearlyInvoices = RecurringInvoice::forYear($this->selectedYear)->get();
        
        return [
            'total_revenue' => $yearlyInvoices->sum('total_amount'),
            'months_data' => collect(range(1, 12))->map(function ($month) use ($yearlyInvoices) {
                $monthInvoices = $yearlyInvoices->filter(fn($inv) => 
                    $inv->scheduled_date->month === $month
                );
                
                return [
                    'month' => $month,
                    'month_name' => now()->month($month)->format('M'),
                    'count' => $monthInvoices->count(),
                    'amount' => $monthInvoices->sum('total_amount'),
                    'draft' => $monthInvoices->where('status', 'draft')->count(),
                    'published' => $monthInvoices->where('status', 'published')->count(),
                ];
            })
        ];
    }

    public function selectMonth(int $month)
    {
        $this->selectedMonth = $month;
    }

    public function editTemplate($templateId)
    {
        $this->dispatch('edit-template', templateId: $templateId);
    }

    public function publishDraft($invoiceId)
    {
        $this->dispatch('publish-draft', invoiceId: $invoiceId);
    }

    public function bulkPublish()
    {
        if (empty($this->selected)) {
            $this->toast()->warning('Warning', 'Pilih minimal satu draft untuk dipublish')->send();
            return;
        }

        $this->toast()
            ->question('Publish Drafts?', count($this->selected) . ' draft akan dipublish ke invoice')
            ->confirm('Ya, Publish', 'confirmBulkPublish')
            ->cancel('Batal')
            ->send();
    }

    public function confirmBulkPublish()
    {
        try {
            $drafts = RecurringInvoice::whereIn('id', $this->selected)
                ->where('status', 'draft')
                ->get();

            foreach ($drafts as $draft) {
                $draft->publish();
            }

            $this->selected = [];
            $this->toast()->success('Berhasil', $drafts->count() . ' draft berhasil dipublish')->send();
        } catch (\Exception $e) {
            $this->toast()->error('Error', 'Gagal publish: ' . $e->getMessage())->send();
        }
    }

    public function render()
    {
        return view('livewire.recurring-invoices.index');
    }
}