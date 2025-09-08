<?php

namespace App\Livewire\RecurringInvoices\Monthly;

use TallStackUi\Traits\Interactions;
use App\Models\RecurringTemplate;
use App\Models\RecurringInvoice;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Carbon\Carbon;

class CreateInvoice extends Component
{
    use Interactions;

    public bool $modal = false;
    public array $invoice = [
        'template_id' => '',
        'scheduled_date' => '',
    ];

    public function mount()
    {
        $this->invoice['scheduled_date'] = now()->format('Y-m-d');
    }

    #[Computed]
    public function availableTemplates()
    {
        return RecurringTemplate::where('status', 'active')
            ->with('client')
            ->orderBy('template_name')
            ->get()
            ->map(fn($template) => [
                'label' => $template->client->name . ' - ' . $template->template_name,
                'value' => $template->id,
                'description' => $template->formatted_total_amount . ' (' . ucfirst($template->frequency) . ')'
            ])
            ->toArray();
    }

    #[Computed]
    public function selectedTemplate()
    {
        if (!$this->invoice['template_id'])
            return null;

        return RecurringTemplate::with('client')->find($this->invoice['template_id']);
    }

    public function checkDuplicate()
    {
        if (!$this->invoice['template_id'] || !$this->invoice['scheduled_date']) {
            return;
        }

        $date = Carbon::parse($this->invoice['scheduled_date']);

        $exists = RecurringInvoice::where('template_id', $this->invoice['template_id'])
            ->whereYear('scheduled_date', $date->year)
            ->whereMonth('scheduled_date', $date->month)
            ->exists();

        if ($exists) {
            $this->toast()
                ->warning('Duplikasi', 'Invoice untuk template ini sudah ada di bulan yang dipilih')
                ->send();
            return true;
        }

        return false;
    }

    public function save()
    {
        $this->validate([
            'invoice.template_id' => 'required|exists:recurring_templates,id',
            'invoice.scheduled_date' => 'required|date',
        ]);

        if ($this->checkDuplicate()) {
            return;
        }

        $template = $this->selectedTemplate;
        $scheduledDate = Carbon::parse($this->invoice['scheduled_date']);

        // Create recurring invoice from template
        RecurringInvoice::create([
            'template_id' => $template->id,
            'client_id' => $template->client_id,
            'scheduled_date' => $scheduledDate,
            'status' => 'draft',
            'invoice_data' => $template->invoice_template
        ]);

        $this->dispatch('invoice-created');
        $this->reset(['invoice', 'modal']);
        $this->invoice['scheduled_date'] = now()->format('Y-m-d');

        $this->toast()
            ->success('Berhasil', 'Invoice berhasil dibuat dari template')
            ->send();
    }

    public function render()
    {
        return view('livewire.recurring-invoices.monthly.create-invoice');
    }
}