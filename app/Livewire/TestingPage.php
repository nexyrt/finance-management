<?php

namespace App\Livewire;

use App\Models\BankTransaction;
use App\Models\InvoiceItem;
use App\Models\Payment;
use Livewire\Component;

class TestingPage extends Component
{
    public $totalIncome;

    public function income()
    {
        $bankIncome = BankTransaction::whereHas('category', function ($query) {
            $query->where('type', 'income');
        })->sum('amount');

        // Ambil semua payments dengan invoice items-nya
        $payments = Payment::with('invoice.items')->get();

        $paymentsIncome = $payments->sum(function ($payment) {
            $invoice = $payment->invoice;

            // Hitung COGS dan Tax Deposits untuk invoice ini
            $itemCogs = $invoice->items->where('is_tax_deposit', false)->sum('cogs_amount');
            $taxDeposits = $invoice->items->where('is_tax_deposit', true)->sum('amount');

            // Total yang harus ditutupi dulu sebelum jadi income
            $costsToCover = $itemCogs + $taxDeposits;

            // Jika payment belum menutupi costs, tidak ada income
            if ($payment->amount <= $costsToCover) {
                return 0;
            }

            // Income adalah payment dikurangi costs yang harus ditutupi
            return $payment->amount - $costsToCover;
        });

        $this->totalIncome = $bankIncome + $paymentsIncome;

        dd([
            'bank_income' => 'Rp ' . number_format($bankIncome, 0, ',', '.'),
            'payments_income' => 'Rp ' . number_format($paymentsIncome, 0, ',', '.'),
            'total_income' => 'Rp ' . number_format($this->totalIncome, 0, ',', '.')
        ]);
    }

    public function render()
    {
        return view('livewire.testing-page');
    }
}