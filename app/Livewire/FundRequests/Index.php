<?php

namespace App\Livewire\FundRequests;

use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{
    public string $activeTab = 'my_requests';

    public function mount(): void
    {
        // If user can view all requests (admin/finance manager), show all requests tab by default
        if (auth()->user()->can('approve fund requests')) {
            $this->activeTab = 'all_requests';
        }
    }

    // Get translated tab name for display
    #[Computed]
    public function translatedTab(): string
    {
        return match($this->activeTab) {
            'my_requests' => __('pages.my_fund_requests'),
            'all_requests' => __('pages.all_fund_requests'),
            default => ucfirst($this->activeTab),
        };
    }

    #[On('fund-request-created')]
    #[On('fund-request-updated')]
    #[On('fund-request-deleted')]
    #[On('fund-request-submitted')]
    #[On('fund-request-reviewed')]
    #[On('fund-request-disbursed')]
    public function refreshTab(): void
    {
        // Only unset stats to refresh counters
        // Don't dispatch to child components - they handle their own refresh
        unset($this->stats);
    }

    #[Computed]
    public function stats(): array
    {
        $user = auth()->user();
        $userId = $user->id;

        // Base queries
        $myRequestsQuery = \App\Models\FundRequest::where('user_id', $userId);
        $allRequestsQuery = \App\Models\FundRequest::query();

        return [
            // My Requests Stats
            'my_total' => $myRequestsQuery->count(),
            'my_draft' => (clone $myRequestsQuery)->where('status', 'draft')->count(),
            'my_pending' => (clone $myRequestsQuery)->where('status', 'pending')->count(),
            'my_approved' => (clone $myRequestsQuery)->where('status', 'approved')->count(),
            'my_disbursed' => (clone $myRequestsQuery)->where('status', 'disbursed')->count(),
            'my_rejected' => (clone $myRequestsQuery)->where('status', 'rejected')->count(),

            // All Requests Stats (for managers/admins)
            'all_total' => $allRequestsQuery->count(),
            'all_pending' => (clone $allRequestsQuery)->where('status', 'pending')->count(),
            'all_approved' => (clone $allRequestsQuery)->where('status', 'approved')->count(),
            'all_disbursed' => (clone $allRequestsQuery)->where('status', 'disbursed')->count(),
            'all_urgent' => (clone $allRequestsQuery)->where('priority', 'urgent')
                ->whereIn('status', ['pending', 'approved'])
                ->count(),

            // Financial Stats
            'total_requested' => (clone $allRequestsQuery)->sum('total_amount'),
            'total_disbursed' => (clone $allRequestsQuery)->where('status', 'disbursed')->sum('total_amount'),
            'pending_amount' => (clone $allRequestsQuery)->whereIn('status', ['pending', 'approved'])->sum('total_amount'),
        ];
    }

    public function render()
    {
        return view('livewire.fund-requests.index');
    }
}
