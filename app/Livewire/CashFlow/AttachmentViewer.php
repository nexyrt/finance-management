<?php

namespace App\Livewire\CashFlow;

use App\Models\Payment;
use App\Models\BankTransaction;
use Livewire\Component;
use Livewire\Attributes\On;

class AttachmentViewer extends Component
{
    public bool $modal = false;
    public ?string $sourceType = null;
    public $attachment = null;

    #[On('view-attachment')]
    public function load($sourceType, $id)
    {
        $this->sourceType = $sourceType;

        if ($sourceType === 'payment') {
            $this->attachment = Payment::find($id);
        } else {
            $this->attachment = BankTransaction::find($id);
        }

        if ($this->attachment && $this->attachment->attachment_path) {
            $this->modal = true;
        }
    }

    public function download()
    {
        if (!$this->attachment || !$this->attachment->attachment_path) {
            return;
        }

        return response()->download(
            storage_path('app/public/' . $this->attachment->attachment_path),
            $this->attachment->attachment_name
        );
    }

    public function render()
    {
        return view('livewire.cash-flow.attachment-viewer');
    }
}