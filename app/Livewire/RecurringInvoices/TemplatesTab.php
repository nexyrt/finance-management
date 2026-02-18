<?php

namespace App\Livewire\RecurringInvoices;

use App\Models\RecurringTemplate;
use Livewire\Component;
use Livewire\Attributes\Computed;
use TallStackUi\Traits\Interactions;

class TemplatesTab extends Component
{
    use Interactions;

    public string $search = '';
    public string $statusFilter = 'active';

    protected $listeners = [
        'template-created' => '$refresh',
        'template-updated' => '$refresh',
        'template-deleted' => '$refresh',
    ];

    #[Computed]
    public function templates()
    {
        $query = RecurringTemplate::with(['client', 'recurringInvoices'])
            ->orderBy('created_at', 'desc');

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('template_name', 'like', "%{$this->search}%")
                    ->orWhereHas('client', function ($clientQuery) {
                        $clientQuery->where('name', 'like', "%{$this->search}%");
                    });
            });
        }

        return $query->get();
    }

    public function restoreTemplate(int $templateId): void
    {
        $template = RecurringTemplate::findOrFail($templateId);
        $template->update(['status' => 'active']);
        unset($this->templates);
        $this->toast()->success('Berhasil', "Template '{$template->template_name}' berhasil diaktifkan kembali")->send();
    }

    public function editTemplate($templateId)
    {
        $this->dispatch('edit-template', templateId: $templateId); // Match EditTemplate listener
    }

    public function viewTemplate($templateId)
    {
        $this->dispatch('view-template', templateId: $templateId);
    }

    public function render()
    {
        return view('livewire.recurring-invoices.templates-tab');
    }
}