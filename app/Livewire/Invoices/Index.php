<?php

namespace App\Livewire\Invoices;

use Livewire\Component;

class Index extends Component
{
    public bool $guideModal = false;

    public function render()
    {
        return view('livewire.invoices.index');
    }
}
