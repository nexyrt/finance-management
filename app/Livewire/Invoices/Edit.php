<?php

namespace App\Livewire\Invoices;

use Livewire\Attributes\On;
use Livewire\Component;

class Edit extends Component
{
    #[On('edit-invoice')]
    public function edit($invoiceId)
    {
        $invoice = \App\Models\Invoice::with('items')->find($invoiceId);
        $data = [
            'invoice' => $invoice,
            'items' => $invoice ? $invoice->items : [],
        ];
        dd($data);
    }

    public function render()
    {
        return view('livewire.invoices.edit');
    }
}
