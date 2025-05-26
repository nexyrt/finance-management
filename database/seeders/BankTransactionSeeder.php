<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BankTransactionSeeder extends Seeder
{
    public function run(): void
    {
        $bankAccounts = BankAccount::all();

        // Use your BankTransactionFactory
        BankTransaction::factory()
            ->count(10)
            ->credit()
            ->create(['bank_account_id' => fn() => $bankAccounts->random()->id]);

        BankTransaction::factory()
            ->count(5)
            ->debit()
            ->create(['bank_account_id' => fn() => $bankAccounts->random()->id]);
    }
}
