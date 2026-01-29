<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class FloatingFeedbackButton extends Component
{
    public function render(): View
    {
        return view('livewire.floating-feedback-button');
    }

    public function openFeedbackForm(): void
    {
        $this->dispatch('open-feedback-form', pageUrl: url()->current());
    }
}
