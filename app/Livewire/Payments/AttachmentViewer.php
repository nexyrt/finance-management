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
        $this->payment = Payment::with(['invoice.client', 'bankAccount'])
            ->find($paymentId);

        if ($this->payment) {
            $this->modal = true;
            return;
        }

        $this->toast()->error('Error', 'Payment tidak ditemukan')->send();
    }

    public function updatedModal(): void
    {
        if (!$this->modal) {
            $this->resetData();
        }
    }

    public function downloadAttachment()
    {
        if (!$this->payment || !$this->payment->attachment_path) {
            $this->toast()->error('Error', 'Lampiran tidak ditemukan')->send();
            return;
        }

        return response()->download(
            storage_path('app/' . $this->payment->attachment_path),
            $this->payment->attachment_name
        );
    }

    public function resetData(): void
    {
        $this->payment = null;
        $this->modal = false;
    }

    public function render()
    {
        return view('livewire.payments.attachment-viewer');
    }
}