<?php

namespace App\Livewire\RecurringInvoices;

use App\Models\RecurringInvoice;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Renderless;
use TallStackUi\Traits\Interactions;

class MonthlyTab extends Component
{
    use Interactions;

    public int $selectedMonth;
    public int $selectedYear;
    public array $selected = [];

    protected $listeners = [
        'invoice-created' => '$refresh',
        'invoice-updated' => '$refresh',
        'invoice-deleted' => '$refresh',
        'invoice-published' => '$refresh',
    ];

    public function mount()
    {
        $this->selectedMonth = now()->month;
        $this->selectedYear = now()->year;
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
        ];
    }

    public function selectMonth(int $month)
    {
        $this->selectedMonth = $month;
    }

    public function editInvoice($invoiceId)
    {
        $this->dispatch('edit-invoice', invoiceId: $invoiceId);
    }

    #[Renderless]
    public function bulkPublish()
    {
        if (empty($this->selected)) {
            $this->toast()->warning('Warning', 'Pilih minimal satu draft')->send();
            return;
        }

        $this->toast()
            ->question('Publish Drafts?', count($this->selected) . ' draft akan dipublish')
            ->confirm(method: 'confirmBulkPublish')
            ->cancel()
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

    #[Renderless]
    public function bulkDelete()
    {
        if (empty($this->selected)) {
            $this->toast()->warning('Warning', 'Pilih minimal satu invoice')->send();
            return;
        }

        $publishedCount = RecurringInvoice::whereIn('id', $this->selected)
            ->where('status', 'published')
            ->count();

        if ($publishedCount > 0) {
            $this->toast()->warning('Cannot Delete', 'Published invoices cannot be deleted')->send();
            return;
        }

        $this->toast()
            ->question('Delete Invoices?', count($this->selected) . ' draft akan dihapus permanen')
            ->confirm(method: 'confirmBulkDelete')
            ->cancel()
            ->send();
    }

    public function confirmBulkDelete()
    {
        try {
            $drafts = RecurringInvoice::whereIn('id', $this->selected)
                ->where('status', 'draft')
                ->get();

            foreach ($drafts as $draft) {
                $draft->delete();
            }

            $this->selected = [];
            $this->toast()->success('Berhasil', $drafts->count() . ' invoice berhasil dihapus')->send();
        } catch (\Exception $e) {
            $this->toast()->error('Error', 'Gagal hapus: ' . $e->getMessage())->send();
        }
    }

    public function render()
    {
        return view('livewire.recurring-invoices.monthly-tab');
    }
}