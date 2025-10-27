<?php

namespace App\Livewire\Reimbursements;

use App\Livewire\Traits\Alert;
use App\Models\Reimbursement;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use Alert, WithFileUploads;

    // Form fields
    public ?string $title = null;
    public ?string $description = null;
    public ?int $amount = null;
    public ?string $expense_date = null;
    public ?string $category = null;
    public $attachment;

    // UI state
    public bool $modal = false;
    public bool $submitOnSave = false;

    public function render(): View
    {
        return view('livewire.reimbursements.create');
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
        $this->validate();

        $reimbursement = new Reimbursement();
        $reimbursement->user_id = Auth::id();
        $reimbursement->title = $this->title;
        $reimbursement->description = $this->description;
        $reimbursement->amount = $this->amount;
        $reimbursement->expense_date = $this->expense_date;
        $reimbursement->category = $this->category;
        $reimbursement->status = $this->submitOnSave ? 'pending' : 'draft';

        // Handle attachment upload
        if ($this->attachment) {
            $path = $this->attachment->store('reimbursements', 'public');
            $reimbursement->attachment_path = $path;
            $reimbursement->attachment_name = $this->attachment->getClientOriginalName();
        }

        $reimbursement->save();

        $this->dispatch('created');
        $this->reset();
        
        $message = $this->submitOnSave 
            ? 'Pengajuan reimbursement berhasil disubmit'
            : 'Pengajuan reimbursement berhasil disimpan sebagai draft';
        
        $this->success($message);
    }

    public function deleteUpload(array $content): void
    {
        if ($this->attachment) {
            rescue(fn () => $this->attachment->delete(), report: false);
            $this->attachment = null;
        }
    }
}