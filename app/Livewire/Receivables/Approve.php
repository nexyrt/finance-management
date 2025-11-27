<?php

namespace App\Livewire\Receivables;

use App\Livewire\Traits\Alert;
use App\Models\BankTransaction;
use App\Models\Receivable;
use App\Models\TransactionCategory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class Approve extends Component
{
    use Alert;

    public bool $modal = false;

    public $receivableId = null;
    public $receivable = null;
    public $action = 'approve'; // 'approve' or 'reject'
    public $notes = null;

    public function render(): View
    {
        return view('livewire.receivables.approve');
    }

    #[On('approve::receivable')]
    public function load(Receivable $receivable): void
    {
        $this->receivableId = $receivable->id;
        $this->receivable = $receivable;
        $this->action = 'approve';
        $this->notes = null;

        $this->modal = true;
    }

    public function approve(): void
    {
        $receivable = Receivable::findOrFail($this->receivableId);

        if ($receivable->status !== 'pending_approval') {
            $this->error('Hanya piutang pending yang bisa disetujui');
            return;
        }

        // Create bank transaction (debit - money out)
        $category = TransactionCategory::where('code', 'FIN-RCV-OUT')->first();

        BankTransaction::create([
            'bank_account_id' => 1, // Default bank account - should be selected
            'amount' => $receivable->principal_amount,
            'transaction_date' => now(),
            'transaction_type' => 'debit',
            'description' => "Piutang diberikan: {$receivable->receivable_number} - {$receivable->debtor?->name}",
            'reference_number' => $receivable->receivable_number,
            'category_id' => $category?->id,
        ]);

        // Update receivable
        $receivable->update([
            'status' => 'active',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'review_notes' => $this->notes,
        ]);

        $this->dispatch('approved');
        $this->reset();
        $this->success('Piutang berhasil disetujui');
    }

    public function reject(): void
    {
        $this->validate([
            'notes' => ['required', 'string', 'max:500'],
        ]);

        $receivable = Receivable::findOrFail($this->receivableId);

        if ($receivable->status !== 'pending_approval') {
            $this->error('Hanya piutang pending yang bisa ditolak');
            return;
        }

        $receivable->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => $this->notes,
        ]);

        $this->dispatch('approved');
        $this->reset();
        $this->success('Piutang berhasil ditolak');
    }
}