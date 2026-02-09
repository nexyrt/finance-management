<?php

namespace App\Livewire\Payments;

use App\Models\Payment;
use Livewire\Component;
use Livewire\Attributes\On;
use Storage;
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

        $this->toast()->error(__('common.error'), __('pages.payment_not_found'))->send();
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
            $this->toast()->error(__('common.error'), __('pages.attachment_not_found'))->send();
            return;
        }

        // Gunakan disk public secara eksplisit
        if (!Storage::disk('public')->exists($this->payment->attachment_path)) {
            $this->toast()->error(__('common.error'), __('pages.file_not_found'))->send();
            return;
        }

        return Storage::disk('public')->download($this->payment->attachment_path, $this->payment->attachment_name);
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