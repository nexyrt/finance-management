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

        $result = $query->selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = 'open' THEN 1 ELSE 0 END) as open,
            SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
            SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved,
            SUM(CASE WHEN type = 'bug' THEN 1 ELSE 0 END) as bugs,
            SUM(CASE WHEN type = 'feature' THEN 1 ELSE 0 END) as features,
            SUM(CASE WHEN type = 'feedback' THEN 1 ELSE 0 END) as feedbacks
        ")->first();

        return [
            'total' => (int) $result->total,
            'open' => (int) $result->open,
            'in_progress' => (int) $result->in_progress,
            'resolved' => (int) $result->resolved,
            'bugs' => (int) $result->bugs,
            'features' => (int) $result->features,
            'feedbacks' => (int) $result->feedbacks,
        ];
    }
}
