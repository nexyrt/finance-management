<?php

namespace App\Livewire;

use App\Models\BankTransaction;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Livewire\Component;

class TestingPage extends Component
{
    public $totalIncome;

    public function income()
    {
        $bankIncome = BankTransaction::whereHas('category', function ($query) {
            $query->where('type', 'income');
        })->sum('amount');

        $totalRevenue = Invoice::sum('total_amount');
        $totalCogs = InvoiceItem::where('is_tax_deposit', false)->sum('cogs_amount');
        $totalTaxDeposits = InvoiceItem::where('is_tax_deposit', true)->sum('amount');

        $totalProfit = $totalRevenue - $totalCogs - $totalTaxDeposits;

        $this->totalIncome = $bankIncome + $totalProfit;

        dd([
            'bank_income' => 'Rp ' . number_format($bankIncome, 0, ',', '.'),
            'total_profit' => 'Rp ' . number_format($totalProfit, 0, ',', '.'),
            'total_income' => 'Rp ' . number_format($this->totalIncome, 0, ',', '.')
        ]);
    }

    public function render()
    {
        return view('livewire.testing-page');
    }
}
