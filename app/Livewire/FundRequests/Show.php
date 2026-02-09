<?php

namespace App\Livewire\FundRequests;

use App\Livewire\Traits\Alert;
use App\Models\FundRequest;
use Livewire\Attributes\On;
use Livewire\Component;

class Show extends Component
{
    use Alert;
    public bool $modal = false;
    public ?FundRequest $fundRequest = null;

    #[On('show::fund-request')]
    public function openModal(int $id): void
    {
        $this->fundRequest = FundRequest::with([
            'user',
            'items.category',
            'reviewer',
            'disburser',
            'bankTransaction',
        ])->findOrFail($id);

        // Check authorization
        $canView = auth()->user()->can('view fund requests');
        $isOwner = $this->fundRequest->user_id === auth()->id();

        if (! $canView && ! $isOwner) {
            $this->toast()->error('Unauthorized', 'You cannot view this fund request.')->send();

            return;
        }

        $this->modal = true;
    }

    public function render()
    {
        return view('livewire.fund-requests.show');
    }
}
