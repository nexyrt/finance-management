<?php

namespace App\Livewire\Feedbacks;

use App\Livewire\Traits\Alert;
use App\Models\AppNotification;
use App\Models\Feedback;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use Alert, WithFileUploads;

    public bool $modal = false;
    public ?string $title = null;
    public ?string $description = null;
    public string $type = 'feedback';
    public string $priority = 'medium';
    public $attachment = null;
    public ?string $pageUrl = null;

    public function render(): View
    {
        return view('livewire.feedbacks.create');
    }

    #[On('open-feedback-form')]
    public function openModal(?string $pageUrl = null): void
    {
        if ($pageUrl) {
            $this->pageUrl = $pageUrl;
        }
        $this->modal = true;
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

    public function messages(): array
    {
        return [
            'title.required' => 'Judul harus diisi',
            'title.max' => 'Judul maksimal 255 karakter',
            'description.required' => 'Deskripsi harus diisi',
            'description.max' => 'Deskripsi maksimal 5000 karakter',
            'attachment.max' => 'Ukuran file maksimal 5MB',
            'attachment.mimes' => 'File harus berformat JPG, PNG, atau PDF',
        ];
    }

    public function save(): void
    {
        $this->validate();

        DB::transaction(function () {
            $attachmentPath = null;
            $attachmentName = null;

            if ($this->attachment) {
                $attachmentPath = $this->attachment->store('feedbacks', 'public');
                $attachmentName = $this->attachment->getClientOriginalName();
            }

            $feedback = Feedback::create([
                'user_id' => auth()->id(),
                'title' => $this->title,
                'description' => $this->description,
                'type' => $this->type,
                'priority' => $this->priority,
                'page_url' => $this->pageUrl,
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachmentName,
                'status' => 'open',
            ]);

            // Notify admins about new feedback
            $this->notifyAdmins($feedback);
        });

        $this->dispatch('feedback-created');
        $this->resetForm();

        $this->success('Feedback berhasil dikirim! Terima kasih atas masukan Anda.');
    }

    private function notifyAdmins(Feedback $feedback): void
    {
        $admins = User::role(['admin', 'finance manager'])->get();

        foreach ($admins as $admin) {
            AppNotification::notify(
                $admin->id,
                'feedback_submitted',
                'Feedback Baru Diterima',
                "{$feedback->type_label} dari {$feedback->user->name}: {$feedback->title}",
                ['feedback_id' => $feedback->id, 'url' => route('feedbacks.index')]
            );
        }
    }

    public function resetForm(): void
    {
        $this->reset(['title', 'description', 'attachment']);
        $this->type = 'feedback';
        $this->priority = 'medium';
        $this->modal = false;
    }

    public function deleteUpload(): void
    {
        $this->attachment = null;
        $this->resetValidation('attachment');
    }
}
