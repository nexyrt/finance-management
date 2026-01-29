<?php

namespace App\Livewire\Feedbacks;

use App\Livewire\Traits\Alert;
use App\Models\Feedback;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class Update extends Component
{
    use Alert, WithFileUploads;

    public bool $modal = false;
    public ?int $feedbackId = null;

    public ?string $title = null;
    public ?string $description = null;
    public string $type = 'feedback';
    public string $priority = 'medium';
    public $attachment = null;
    public ?string $existingAttachment = null;
    public bool $removeAttachment = false;

    public function render(): View
    {
        return view('livewire.feedbacks.update');
    }

    #[On('edit::feedback')]
    public function load(int $id): void
    {
        $feedback = Feedback::find($id);

        if (!$feedback) {
            $this->error('Feedback tidak ditemukan');
            return;
        }

        // Check permission - user can only edit their own and only if status is open
        if ($feedback->user_id !== auth()->id()) {
            $this->error('Anda tidak memiliki akses untuk mengedit feedback ini');
            return;
        }

        if (!$feedback->canEdit()) {
            $this->error('Feedback yang sudah diproses tidak dapat diedit');
            return;
        }

        $this->feedbackId = $id;
        $this->title = $feedback->title;
        $this->description = $feedback->description;
        $this->type = $feedback->type;
        $this->priority = $feedback->priority;
        $this->existingAttachment = $feedback->attachment_name;
        $this->removeAttachment = false;
        $this->attachment = null;

        $this->modal = true;
    }

    #[Computed]
    public function feedback(): ?Feedback
    {
        return $this->feedbackId ? Feedback::find($this->feedbackId) : null;
    }

    #[Computed]
    public function types(): array
    {
        return Feedback::types();
    }

    #[Computed]
    public function priorities(): array
    {
        return Feedback::priorities();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'type' => ['required', 'in:bug,feature,feedback'],
            'priority' => ['required', 'in:low,medium,high,critical'],
            'attachment' => ['nullable', 'file', 'max:5120', 'mimes:jpg,jpeg,png,pdf'],
        ];
    }

    public function save(): void
    {
        $this->validate();

        $feedback = Feedback::find($this->feedbackId);

        if (!$feedback || !$feedback->canEdit()) {
            $this->error('Feedback tidak dapat diedit');
            return;
        }

        DB::transaction(function () use ($feedback) {
            $data = [
                'title' => $this->title,
                'description' => $this->description,
                'type' => $this->type,
                'priority' => $this->priority,
            ];

            // Handle attachment
            if ($this->removeAttachment && $feedback->attachment_path) {
                if (Storage::exists($feedback->attachment_path)) {
                    Storage::delete($feedback->attachment_path);
                }
                $data['attachment_path'] = null;
                $data['attachment_name'] = null;
            }

            if ($this->attachment) {
                // Delete old attachment if exists
                if ($feedback->attachment_path && Storage::exists($feedback->attachment_path)) {
                    Storage::delete($feedback->attachment_path);
                }

                $data['attachment_path'] = $this->attachment->store('feedbacks', 'public');
                $data['attachment_name'] = $this->attachment->getClientOriginalName();
            }

            $feedback->update($data);
        });

        $this->dispatch('feedback-updated');
        $this->close();

        $this->success('Feedback berhasil diperbarui');
    }

    public function markRemoveAttachment(): void
    {
        $this->removeAttachment = true;
        $this->existingAttachment = null;
    }

    public function deleteUpload(): void
    {
        $this->attachment = null;
        $this->resetValidation('attachment');
    }

    public function close(): void
    {
        $this->modal = false;
        $this->reset(['feedbackId', 'title', 'description', 'type', 'priority', 'attachment', 'existingAttachment', 'removeAttachment']);
    }
}
