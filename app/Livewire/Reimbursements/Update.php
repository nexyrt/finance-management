<?php

namespace App\Livewire\Reimbursements;

use App\Livewire\Traits\Alert;
use App\Models\Reimbursement;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class Update extends Component
{
    use Alert, WithFileUploads;

    // Form fields
    public ?Reimbursement $reimbursement = null;

    public ?string $title = null;

    public ?string $description = null;

    public ?int $amount = null;

    public ?string $expense_date = null;

    public ?string $category = null;

    public $attachment;

    public bool $removeExistingAttachment = false;

    // UI state
    public bool $modal = false;

    public function render(): View
    {
        return view('livewire.reimbursements.update');
    }

    #[On('load::reimbursement')]
    public function load(Reimbursement $reimbursement): void
    {
        // Only allow editing draft or rejected
        if (! $reimbursement->canEdit()) {
            $this->error('Pengajuan tidak dapat diedit');

            return;
        }

        // Only allow editing own reimbursements
        if ($reimbursement->user_id !== Auth::id()) {
            $this->error('Anda tidak memiliki akses');

            return;
        }

        $this->reimbursement = $reimbursement;
        $this->title = $reimbursement->title;
        $this->description = $reimbursement->description;
        $this->amount = $reimbursement->amount;
        $this->expense_date = $reimbursement->expense_date?->format('Y-m-d');
        $this->category = $reimbursement->category;
        $this->modal = true;
    }

    #[Computed]
    public function categories(): array
    {
        return Reimbursement::categories();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'amount' => ['required', 'integer', 'min:1'],
            'expense_date' => ['required', 'date', 'before_or_equal:today'],
            'category' => ['required', 'string', 'in:transport,meals,office_supplies,communication,accommodation,medical,other'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'expense_date.before_or_equal' => 'Tanggal pengeluaran tidak boleh melebihi hari ini.',
            'attachment.mimes' => 'File harus berformat JPG, PNG, atau PDF.',
            'attachment.max' => 'Ukuran file maksimal 2MB.',
        ];
    }

    public function save(): void
    {
        if (! $this->reimbursement) {
            return;
        }

        $this->validate();

        $this->reimbursement->title = $this->title;
        $this->reimbursement->description = $this->description;
        $this->reimbursement->amount = $this->amount;
        $this->reimbursement->expense_date = $this->expense_date;
        $this->reimbursement->category = $this->category;

        // Handle attachment removal
        if ($this->removeExistingAttachment && $this->reimbursement->hasAttachment()) {
            \Storage::disk('public')->delete($this->reimbursement->attachment_path);
            $this->reimbursement->attachment_path = null;
            $this->reimbursement->attachment_name = null;
        }

        // Handle new attachment upload
        if ($this->attachment) {
            // Delete old attachment if exists
            if ($this->reimbursement->hasAttachment()) {
                \Storage::disk('public')->delete($this->reimbursement->attachment_path);
            }

            $path = $this->attachment->store('reimbursements', 'public');
            $this->reimbursement->attachment_path = $path;
            $this->reimbursement->attachment_name = $this->attachment->getClientOriginalName();
        }

        $this->reimbursement->save();

        $this->dispatch('updated');
        $this->resetExcept('reimbursement');
        $this->success('Pengajuan reimbursement berhasil diperbarui');
    }

    public function removeAttachment(): void
    {
        $this->removeExistingAttachment = true;
    }

    public function deleteUpload(array $content): void
    {
        if ($this->attachment) {
            rescue(fn () => $this->attachment->delete(), report: false);
            $this->attachment = null;
        }
    }
}
