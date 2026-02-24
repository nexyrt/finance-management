<?php

namespace App\Livewire\RecurringInvoices;

use App\Models\RecurringTemplate;
use App\Models\RecurringInvoice;
use Illuminate\Support\Facades\DB;
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
        // DB-level sum on JSON field — avoids loading all rows into PHP
        return RecurringInvoice::forYear(now()->year)
            ->sum(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(invoice_data, '$.total_amount'))"));
    }

    public function render()
    {
        return view('livewire.recurring-invoices.index');
    }
}