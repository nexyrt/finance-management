<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class TestingPage extends Component
{
    public string $activeTab = 'tab1';

    public function render(): View
    {
        return view('livewire.testing-page');
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
    }
}