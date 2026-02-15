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
            $this->toast()->error(__('common.error'), __('feedback.not_found'))->send();
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
            $this->toast()->error(__('common.error'), __('feedback.no_delete_permission'))->send();
            return;
        }

        $this->dialog()
            ->question(__('feedback.delete_title'), __('feedback.delete_confirm_message', ['title' => $feedback->title]))
            ->confirm(__('feedback.delete_confirm'), 'delete', $id)
            ->cancel(__('common.cancel'))
            ->send();
    }

    public function delete($id): void
    {
        $feedback = Feedback::find($id);

        if (!$feedback) {
            $this->toast()->error(__('common.error'), __('feedback.not_found'))->send();
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
            $this->toast()->error(__('common.error'), __('feedback.no_delete_permission'))->send();
            return;
        }

        $feedback->delete();

        $this->dispatch('feedback-deleted');
        $this->toast()->success(__('common.success'), __('feedback.deleted_success'))->send();
    }
}
