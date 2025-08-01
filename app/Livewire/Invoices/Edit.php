<?php

namespace App\Livewire\Invoices;

use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Edit extends Component
{
    use Interactions;

    public function render()
    {
        return view('livewire.invoices.edit');
    }
}