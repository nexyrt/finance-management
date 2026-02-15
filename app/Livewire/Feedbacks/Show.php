<?php

namespace App\Livewire\Feedbacks;

use App\Models\Feedback;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Show extends Component
{
    use Interactions;

    public bool $modal = false;
    public ?int $feedbackId = null;

    public function render(): View
    {
        return view('livewire.feedbacks.show');
    }

    #[On('show::feedback')]
    public function load(int $id): void
    {
        $feedback = Feedback::with(['user', 'responder'])->find($id);

        if (!$feedback) {
            $this->toast()->error(__('common.error'), __('feedback.not_found'))->send();
            return;
        }

        // Check permission - user can only view their own or admin can view all
        if ($feedback->user_id !== auth()->id() && !auth()->user()->can('manage feedbacks')) {
            $this->toast()->error(__('common.error'), __('feedback.no_view_permission'))->send();
            return;
        }

        $this->feedbackId = $id;
        $this->modal = true;
    }

    #[Computed]
    public function feedback(): ?Feedback
    {
        return $this->feedbackId
            ? Feedback::with(['user', 'responder'])->find($this->feedbackId)
            : null;
    }

    public function close(): void
    {
        $this->modal = false;
        $this->feedbackId = null;
    }

    public function editFeedback(): void
    {
        $this->dispatch('edit::feedback', id: $this->feedbackId);
        $this->close();
    }

    public function respondFeedback(): void
    {
        $this->dispatch('respond::feedback', id: $this->feedbackId);
        $this->close();
    }
}
