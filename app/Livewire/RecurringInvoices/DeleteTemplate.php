<?php

namespace App\Livewire\RecurringInvoices;

use TallStackUi\Traits\Interactions;
use App\Models\RecurringTemplate;
use Livewire\Component;
use Livewire\Attributes\Renderless;

class DeleteTemplate extends Component
{
    use Interactions;

    public RecurringTemplate $template;

    public function render(): string
    {
        return <<<'HTML'
        <div>
            <x-button.circle icon="trash" color="red" size="sm" wire:click="confirm" outline />
        </div>
        HTML;
    }

    #[Renderless]
    public function confirm(): void
    {
        $recurringCount = $this->template->recurringInvoices()->count();
        $publishedCount = $this->template->recurringInvoices()->where('status', 'published')->count();

        if ($publishedCount > 0) {
            $this->dialog()
                ->question('Arsipkan Template?', "Template memiliki {$publishedCount} invoice yang sudah dipublish. Arsipkan agar tidak di-generate lagi?")
                ->confirm('Arsipkan', 'archive')
                ->cancel('Batal')
                ->send();
            return;
        }

        $message = $recurringCount > 0
            ? "Template '{$this->template->template_name}' dan {$recurringCount} draft invoice akan dihapus permanen"
            : "Template '{$this->template->template_name}' akan dihapus permanen";

        $this->dialog()
            ->question('Hapus Template?', $message)
            ->confirm(method: 'delete')
            ->cancel()
            ->send();
    }

    public function archive(): void
    {
        $this->template->update(['status' => 'archived']);
        $this->dispatch('template-deleted');
        $this->toast()->success('Berhasil', "Template '{$this->template->template_name}' berhasil diarsipkan")->send();
    }

    public function delete(): void
    {
        // Delete all recurring invoices (drafts only)
        $this->template->recurringInvoices()->where('status', 'draft')->delete();

        // Delete template
        $templateName = $this->template->template_name;
        $this->template->delete();

        $this->dispatch('template-deleted');
        $this->toast()->success('Berhasil', "Template '{$templateName}' berhasil dihapus")->send();
    }
}