<?php

namespace App\Livewire\FundRequests;

use App\Livewire\Traits\Alert;
use App\Models\FundRequest;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Show extends Component
{
    use Alert;
    public bool $modal = false;
    public ?int $fundRequestId = null;

    #[On('show::fund-request')]
    public function openModal(int $id): void
    {
        $fundRequest = FundRequest::findOrFail($id);

        // Check authorization
        $canView = auth()->user()->can('view fund requests');
        $isOwner = $fundRequest->user_id === auth()->id();

        if (! $canView && ! $isOwner) {
            $this->toast()->error('Unauthorized', 'You cannot view this fund request.')->send();

            return;
        }

        $this->fundRequestId = $id;
        $this->modal = true;
    }

    #[Computed]
    public function fundRequest(): ?FundRequest
    {
        return $this->fundRequestId
            ? FundRequest::with([
                'user',
                'items.category.parent',
                'reviewer',
                'disburser',
                'bankTransaction',
            ])->find($this->fundRequestId)
            : null;
    }

    public function render(): View
    {
        return view('livewire.fund-requests.show', [
            'fundRequest' => $this->fundRequest,
        ]);
    }
}
