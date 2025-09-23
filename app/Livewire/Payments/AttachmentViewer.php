<?php

namespace App\Livewire\Payments;

use App\Models\Payment;
use Livewire\Component;
use Livewire\Attributes\On;
use TallStackUi\Traits\Interactions;

class AttachmentViewer extends Component
{
    use Interactions;

    public ?Payment $payment = null;
    public bool $modal = false;

    #[On('show-payment-attachment')]
    public function show(int $paymentId): void
    {
        $this->payment = Payment::with(['invoice', 'bankAccount'])->find($paymentId);

        if (!$this->payment || !$this->payment->hasAttachment()) {
            $this->toast()->error('Error', 'Attachment tidak ditemukan')->send();
            return;
        }

        $this->modal = true;
    }

    public function render()
    {
        return view('livewire.payments.attachment-viewer');
    }
}