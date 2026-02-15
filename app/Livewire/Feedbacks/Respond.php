<?php

namespace App\Livewire\Feedbacks;

use App\Livewire\Traits\Alert;
use App\Models\AppNotification;
use App\Models\Feedback;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Respond extends Component
{
    use Alert;

    public bool $modal = false;
    public ?int $feedbackId = null;
    public ?string $response = null;
    public string $status = 'in_progress';

    public function render(): View
    {
        return view('livewire.feedbacks.respond');
    }

    #[On('respond::feedback')]
    public function load(int $id): void
    {
        if (!auth()->user()->can('respond feedbacks')) {
            $this->error(__('feedback.no_respond_permission'));
            return;
        }

        $feedback = Feedback::find($id);

        if (!$feedback) {
            $this->error(__('feedback.not_found'));
            return;
        }

        if (!$feedback->canRespond()) {
            $this->error(__('feedback.cannot_respond'));
            return;
        }

        $this->feedbackId = $id;
        $this->response = $feedback->admin_response;
        $this->status = $feedback->status === 'open' ? 'in_progress' : $feedback->status;
        $this->modal = true;
    }

    #[Computed]
    public function feedback(): ?Feedback
    {
        return $this->feedbackId
            ? Feedback::with('user')->find($this->feedbackId)
            : null;
    }

    #[Computed]
    public function statusOptions(): array
    {
        return [
            ['label' => __('feedback.status_in_progress'), 'value' => 'in_progress'],
            ['label' => __('feedback.status_resolved'), 'value' => 'resolved'],
            ['label' => __('feedback.status_closed'), 'value' => 'closed'],
        ];
    }

    public function rules(): array
    {
        return [
            'response' => ['required', 'string', 'max:5000'],
            'status' => ['required', 'in:in_progress,resolved,closed'],
        ];
    }

    public function messages(): array
    {
        return [
            'response.required' => __('feedback.validation.response_required'),
            'response.max' => __('feedback.validation.response_max'),
        ];
    }

    public function save(): void
    {
        $this->validate();

        $feedback = Feedback::find($this->feedbackId);

        if (!$feedback) {
            $this->error(__('feedback.not_found'));
            return;
        }

        $feedback->respond(auth()->id(), $this->response, $this->status);

        // Notify the user who submitted the feedback
        AppNotification::notify(
            $feedback->user_id,
            'feedback_responded',
            __('feedback.notification_responded_title'),
            __('feedback.notification_responded_message', [
                'title' => $feedback->title,
                'responder' => auth()->user()->name,
            ]),
            ['feedback_id' => $feedback->id, 'url' => route('feedbacks.index')]
        );

        $this->dispatch('feedback-responded');
        $this->close();

        $this->success(__('feedback.response_sent'));
    }

    public function close(): void
    {
        $this->modal = false;
        $this->reset(['feedbackId', 'response', 'status']);
    }
}
