<?php

namespace App\Livewire\FundRequests;

use App\Livewire\Traits\Alert;
use App\Models\FundRequest;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Review extends Component
{
    use Alert;
    public bool $modal = false;
    public ?int $fundRequestId = null;
    public string $reviewNotes = '';

    protected function rules(): array
    {
        return [
            'reviewNotes' => 'nullable|string|max:1000',
        ];
    }

    #[On('review::fund-request')]
    public function openModal(int $id): void
    {
        $fundRequest = FundRequest::findOrFail($id);

        // Check if can review
        if (! $fundRequest->canReview()) {
            $this->toast()->error(__('common.error'), __('pages.cannot_review_fund_request'))->send();

            return;
        }

        // Check permission
        if (! auth()->user()->can('approve fund requests')) {
            $this->toast()->error(__('common.error'), __('pages.unauthorized_review_fund_request'))->send();

            return;
        }

        $this->fundRequestId = $id;
        $this->reset('reviewNotes');
        $this->modal = true;
    }

    #[Computed]
    public function fundRequest(): ?FundRequest
    {
        return $this->fundRequestId
            ? FundRequest::with(['user', 'items.category.parent'])->find($this->fundRequestId)
            : null;
    }

    public function approve(): void
    {
        $this->validate();

        $fundRequest = $this->fundRequest;

        if (! $fundRequest || ! $fundRequest->canReview()) {
            $this->toast()->error(__('common.error'), __('pages.cannot_review_fund_request'))->send();
            $this->modal = false;

            return;
        }

        $fundRequest->approve(auth()->id(), $this->reviewNotes);

        $this->modal = false;
        $this->reset();

        $this->toast()->success(__('common.success'), __('pages.fund_request_approved'))->send();
        $this->dispatch('fund-request-reviewed');
    }

    public function reject(): void
    {
        $this->validate([
            'reviewNotes' => 'required|string|max:1000',
        ], [
            'reviewNotes.required' => __('pages.review_notes_required'),
        ]);

        $fundRequest = $this->fundRequest;

        if (! $fundRequest || ! $fundRequest->canReview()) {
            $this->toast()->error(__('common.error'), __('pages.cannot_review_fund_request'))->send();
            $this->modal = false;

            return;
        }

        $fundRequest->reject(auth()->id(), $this->reviewNotes);

        $this->modal = false;
        $this->reset();

        $this->toast()->success(__('common.success'), __('pages.fund_request_rejected'))->send();
        $this->dispatch('fund-request-reviewed');
    }

    public function render(): View
    {
        return view('livewire.fund-requests.review', [
            'fundRequest' => $this->fundRequest,
        ]);
    }
}
