<?php

namespace App\Livewire;

use App\Models\Invoice;
use Livewire\Component;

class TestingPage extends Component
{
    public $date;
    public $selectedInvoice = null;
    public $invoiceItems = [];
    public $invoicePayments = [];
    public $isLoadingInvoiceDetails = false;

    public function getInvoiceItems($id)
    {
        $this->isLoadingInvoiceDetails = true;
        
        try {
            $this->selectedInvoice = Invoice::with(['client', 'items'])
                ->findOrFail($id);
            
            $this->invoiceItems = $this->selectedInvoice->items()->get();
            $this->invoicePayments = $this->selectedInvoice->payments()->with('bankAccount')->get();
        } catch (\Exception $e) {
            $this->selectedInvoice = null;
            $this->invoiceItems = [];
            $this->invoicePayments = [];
        } finally {
            $this->isLoadingInvoiceDetails = false;
        }
    }

    public function getInvoicesProperty()
    {
        $query = Invoice::query();

        // filter date range
        if ($this->date && str_contains($this->date, ' to ')) {
            [$startDate, $endDate] = explode(' to ', $this->date);

            $query->whereBetween('issue_date', [$startDate, $endDate]);
        }

        // Urutkan berdasarkan tanggal terbaru
        return $query->orderByDesc('issue_date')->get();
    }

    public function render()
    {
        return view(
            'livewire.testing-page',
            [
                'invoices' => $this->invoices,
            ]
        );
    }
}
