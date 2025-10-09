<?php

namespace App\Livewire;

use App\Models\BankTransaction;
use App\Models\Invoice;
use Livewire\Component;

class TestingPage extends Component
{
    public $totalIncome;

    public function income()
    {
        $bankIncome = BankTransaction::whereHas('category', function ($query) {
            $query->where('type', 'income');
        })->sum('amount');

        // Ambil semua invoice dengan items dan payments
        $invoices = Invoice::with(['items', 'payments'])->get();

        // Hitung total profit
        $totalProfit = $invoices->sum(function ($invoice) {
            $taxDeposits = $invoice->items->where('is_tax_deposit', true)->sum('amount');
            $itemCogs = $invoice->items->where('is_tax_deposit', false)->sum('cogs_amount');

            return $invoice->total_amount - $taxDeposits - $itemCogs;
        });

        // Hitung outstanding profit (sama persis dengan stats)
        $outstandingProfit = $invoices->sum(function ($invoice) {
            $itemCogs = $invoice->items->where('is_tax_deposit', false)->sum('cogs_amount');
            $taxDeposits = $invoice->items->where('is_tax_deposit', true)->sum('amount');
            $invoiceRevenue = $invoice->total_amount;
            $invoiceProfit = $invoiceRevenue - $taxDeposits - $itemCogs;

            if ($invoiceProfit <= 0) {
                return 0;
            }

            $totalPaid = $invoice->payments->sum('amount');

            if ($totalPaid <= ($itemCogs + $taxDeposits)) {
                return $invoiceProfit;
            }

            $realizedProfit = min($totalPaid - ($itemCogs + $taxDeposits), $invoiceProfit);

            return max(0, $invoiceProfit - $realizedProfit);
        });

        // Paid profit = total profit - outstanding profit
        $paidProfit = $totalProfit - $outstandingProfit;

        $this->totalIncome = $bankIncome + $paidProfit;

        dd([
            'bank_income' => 'Rp '.number_format($bankIncome, 0, ',', '.'),
            'payments_income' => 'Rp '.number_format($paidProfit, 0, ',', '.'),
            'total_income' => 'Rp '.number_format($totalProfit, 0, ',', '.'),
        ]);
    }

    public function render()
    {
        return view('livewire.testing-page');
    }
}
