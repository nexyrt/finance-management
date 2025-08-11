<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\BankTransaction;
use Carbon\Carbon;

class TestingPage extends Component
{
    public $chartData = [];

    public function mount()
    {
        $this->chartData = $this->getChartData();
    }

    private function getChartData()
    {
        $transactions = BankTransaction::whereYear('transaction_date', now()->year)
            ->get()
            ->groupBy(function ($transaction) {
                return Carbon::parse($transaction->transaction_date)->format('m');
            });

        $monthlyData = [];
        
        for ($month = 1; $month <= 12; $month++) {
            $monthKey = str_pad($month, 2, '0', STR_PAD_LEFT);
            $monthTransactions = $transactions->get($monthKey, collect());
            
            $income = $monthTransactions->where('transaction_type', 'credit')->sum('amount') / 1000;
            $expense = $monthTransactions->where('transaction_type', 'debit')->sum('amount') / 1000;
            
            $monthlyData[] = [
                'month' => Carbon::create()->month($month)->format('M'),
                'income' => (int) $income,
                'expense' => (int) $expense
            ];
        }
        
        return $monthlyData;
    }

    public function refreshData()
    {
        $this->chartData = $this->getChartData();
    }

    public function render()
    {
        return view('livewire.testing-page');
    }
}