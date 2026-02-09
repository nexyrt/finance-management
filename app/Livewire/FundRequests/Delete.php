<?php

namespace App\Livewire\FundRequests;

use App\Livewire\Traits\Alert;
use App\Models\FundRequest;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;

class Delete extends Component
{
    use Alert;

    public bool $modal = false;
    public ?FundRequest $fundRequest = null;

    #[On('delete::fund-request')]
    public function openModal(int $id): void
    {
        $this->fundRequest = FundRequest::with('items')->findOrFail($id);

        // Check if can delete
        if (! $this->fundRequest->canDelete()) {
            $this->toast()->error(__('common.error'), __('pages.cannot_delete_fund_request'))->send();

            return;
        }

        // Check authorization (own request or admin)
        if ($this->fundRequest->user_id !== auth()->id() && ! auth()->user()->hasRole('admin')) {
            $this->toast()->error(__('common.error'), __('pages.unauthorized_delete_fund_request'))->send();

            return;
        }

        $this->modal = true;
    }

    public function delete(): void
    {
        if (! $this->fundRequest->canDelete()) {
            $this->toast()->error(__('common.error'), __('pages.cannot_delete_fund_request'))->send();
            $this->modal = false;

            return;
        }

        // Check authorization again
        if ($this->fundRequest->user_id !== auth()->id() && ! auth()->user()->hasRole('admin')) {
            $this->toast()->error(__('common.error'), __('pages.unauthorized_delete_fund_request'))->send();
            $this->modal = false;

            return;
        }

        // Delete attachment if exists
        if ($this->fundRequest->attachment_path) {
            Storage::disk('public')->delete($this->fundRequest->attachment_path);
        }

        // Delete fund request (items will be cascade deleted)
        $this->fundRequest->delete();

        $this->modal = false;
        $this->reset();

        $this->toast()->success(__('common.success'), __('pages.fund_request_deleted'))->send();
        $this->dispatch('fund-request-deleted');
    }

    public function render()
    {
        return view('livewire.fund-requests.delete');
    }
}
