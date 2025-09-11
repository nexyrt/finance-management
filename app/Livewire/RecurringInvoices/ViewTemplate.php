<?php

namespace App\Livewire\RecurringInvoices;

use TallStackUi\Traits\Interactions;
use App\Models\RecurringTemplate;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

class ViewTemplate extends Component
{
    use Interactions;

    public ?RecurringTemplate $template = null;
    public bool $modal = false;

    #[On('view-template')]
    public function load($templateId): void
    {
        $this->template = RecurringTemplate::with(['client', 'recurringInvoices'])->find($templateId);

        if (!$this->template) {
            $this->toast()->error('Error', 'Template tidak ditemukan')->send();
            return;
        }

        $this->modal = true;
    }

    #[Computed]
    public function templateItems(): array
    {
        return $this->template->invoice_template['items'] ?? [];
    }

    #[Computed]
    public function invoiceStats(): array
    {
        $invoices = $this->template->recurringInvoices;

        return [
            'total' => $invoices->count(),
            'published' => $invoices->where('status', 'published')->count(),
            'draft' => $invoices->where('status', 'draft')->count(),
            'total_revenue' => $invoices->where('status', 'published')->sum('total_amount'),
            'projected_revenue' => $invoices->sum('total_amount'),
        ];
    }

    public function editTemplate(): void
    {
        if (!$this->template) {
            $this->toast()->error('Error', 'Template tidak ditemukan')->send();
            return;
        }

        $this->modal = false;
        $this->dispatch('edit-template', templateId: $this->template->id);
    }

    #[Computed]
    public function nextScheduledInvoices(): \Illuminate\Support\Collection
    {
        return $this->template->recurringInvoices()
            ->where('status', 'draft')
            ->where('scheduled_date', '>=', now())
            ->orderBy('scheduled_date')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.recurring-invoices.view-template');
    }
}