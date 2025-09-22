<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class BankTransactionSeeder extends Seeder
{
    public function run(): void
    {
        $bankAccounts = BankAccount::all();

        // Generate transactions for each month of 2025
        for ($month = 1; $month <= 12; $month++) {
            $startDate = Carbon::create(2025, $month, 1);
            $endDate = $startDate->copy()->endOfMonth();

            // Random number of transactions per month (5-10 for each account)
            foreach ($bankAccounts as $account) {
                $transactionCount = rand(5, 10);

                for ($i = 0; $i < $transactionCount; $i++) {
                    // Random date within the month
                    $transactionDate = $startDate->copy()->addDays(rand(0, $endDate->day - 1));

                    // 60% credit, 40% debit for more realistic balance
                    $isCredit = rand(1, 100) <= 60;

                    BankTransaction::factory()
                        ->when($isCredit, fn($factory) => $factory->credit(), fn($factory) => $factory->debit())
                        ->create([
                            'bank_account_id' => $account->id,
                            'transaction_date' => $transactionDate,
                            'created_at' => $transactionDate->copy()->addHours(rand(8, 18)),
                            'updated_at' => $transactionDate->copy()->addHours(rand(8, 18)),
                        ]);
                }
            }
        }
    }
}