<?php

namespace App\Livewire\Feedbacks;

use App\Models\Feedback;
use Livewire\Attributes\On;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Delete extends Component
{
    use Interactions;

    public function render(): string
    {
        return <<<'HTML'
        <div></div>
        HTML;
    }

    #[On('delete::feedback')]
    public function confirm(int $id): void
    {
        $feedback = Feedback::find($id);

        if (!$feedback) {
            $this->toast()->error('Error', 'Feedback tidak ditemukan')->send();
            return;
        }

        // Check permission
        $canDelete = false;

        if (auth()->user()->can('manage feedbacks')) {
            $canDelete = true;
        } elseif ($feedback->user_id === auth()->id() && $feedback->canDelete()) {
            $canDelete = true;
        }

        if (!$canDelete) {
            $this->toast()->error('Error', 'Anda tidak memiliki akses untuk menghapus feedback ini')->send();
            return;
        }

        $this->dialog()
            ->question("Hapus Feedback?", "Feedback \"{$feedback->title}\" akan dihapus permanen. Aksi ini tidak dapat dibatalkan.")
            ->confirm('Ya, Hapus', 'delete', [$id])
            ->cancel('Batal')
            ->send();
    }

    public function delete(int $id): void
    {
        $feedback = Feedback::find($id);

        if (!$feedback) {
            $this->toast()->error('Error', 'Feedback tidak ditemukan')->send();
            return;
        }

        // Double check permission
        $canDelete = false;

        if (auth()->user()->can('manage feedbacks')) {
            $canDelete = true;
        } elseif ($feedback->user_id === auth()->id() && $feedback->canDelete()) {
            $canDelete = true;
        }

        if (!$canDelete) {
            $this->toast()->error('Error', 'Anda tidak memiliki akses untuk menghapus feedback ini')->send();
            return;
        }

        $feedback->delete();

        $this->dispatch('feedback-deleted');
        $this->toast()->success('Berhasil', 'Feedback berhasil dihapus')->send();
    }
}
