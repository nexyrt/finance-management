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
            $this->error('Anda tidak memiliki akses untuk merespon feedback');
            return;
        }

        $feedback = Feedback::find($id);

        if (!$feedback) {
            $this->error('Feedback tidak ditemukan');
            return;
        }

        if (!$feedback->canRespond()) {
            $this->error('Feedback ini tidak dapat direspon');
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
            ['label' => 'In Progress', 'value' => 'in_progress'],
            ['label' => 'Resolved', 'value' => 'resolved'],
            ['label' => 'Closed', 'value' => 'closed'],
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
            'response.required' => 'Respon harus diisi',
            'response.max' => 'Respon maksimal 5000 karakter',
        ];
    }

    public function save(): void
    {
        $this->validate();

        $feedback = Feedback::find($this->feedbackId);

        if (!$feedback) {
            $this->error('Feedback tidak ditemukan');
            return;
        }

        $feedback->respond(auth()->id(), $this->response, $this->status);

        // Notify the user who submitted the feedback
        AppNotification::notify(
            $feedback->user_id,
            'feedback_responded',
            'Feedback Direspon',
            "Feedback Anda \"{$feedback->title}\" telah direspon oleh " . auth()->user()->name,
            ['feedback_id' => $feedback->id, 'url' => route('feedbacks.index')]
        );

        $this->dispatch('feedback-responded');
        $this->close();

        $this->success('Respon berhasil dikirim');
    }

    public function close(): void
    {
        $this->modal = false;
        $this->reset(['feedbackId', 'response', 'status']);
    }
}
