<?php

namespace App\Livewire\Feedbacks;

use App\Models\Feedback;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{
    #[On('feedback-created')]
    #[On('feedback-updated')]
    #[On('feedback-deleted')]
    #[On('feedback-responded')]
    public function refresh(): void
    {
        unset($this->stats);
    }

    public function render(): View
    {
        return view('livewire.feedbacks.index');
    }

    public function canManageFeedbacks(): bool
    {
        return auth()->check() && auth()->user()->can('manage feedbacks');
    }

    #[Computed]
    public function stats(): array
    {
        $query = $this->canManageFeedbacks()
            ? Feedback::query()
            : Feedback::forUser(auth()->id());

        $total = (clone $query)->count();
        $open = (clone $query)->byStatus('open')->count();
        $inProgress = (clone $query)->byStatus('in_progress')->count();
        $resolved = (clone $query)->byStatus('resolved')->count();
        $bugs = (clone $query)->byType('bug')->count();
        $features = (clone $query)->byType('feature')->count();
        $feedbacks = (clone $query)->byType('feedback')->count();

        return [
            'total' => $total,
            'open' => $open,
            'in_progress' => $inProgress,
            'resolved' => $resolved,
            'bugs' => $bugs,
            'features' => $features,
            'feedbacks' => $feedbacks,
        ];
    }
}
