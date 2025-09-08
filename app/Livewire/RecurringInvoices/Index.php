<?php

namespace App\Livewire\RecurringInvoices;

use App\Models\RecurringTemplate;
use App\Models\RecurringInvoice;
use Livewire\Component;
use Livewire\Attributes\Computed;

class Index extends Component
{
    #[Computed]
    public function activeTemplatesCount()
    {
        return RecurringTemplate::where('status', 'active')->count();
    }

    #[Computed]
    public function totalProjectedRevenue()
    {
        return RecurringInvoice::forYear(now()->year)
            ->get()
            ->sum('total_amount');
    }

    public function render()
    {
        return view('livewire.recurring-invoices.index');
    }
}