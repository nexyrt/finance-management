<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use Illuminate\Database\Seeder;

class BankAccountSeeder extends Seeder
{
    /**
     * Seed the bank_accounts table with test data
     */
    public function run(): void
    {
        $accounts = [
            [
                'account_name' => 'BCA - Operasional',
                'account_number' => '1234567890',
                'bank_name' => 'Bank Central Asia',
                'branch' => 'Jakarta Pusat',
                'initial_balance' => 50000000, // Rp 50 juta
            ],
            [
                'account_name' => 'Mandiri - Payroll',
                'account_number' => '9876543210',
                'bank_name' => 'Bank Mandiri',
                'branch' => 'Jakarta Selatan',
                'initial_balance' => 30000000, // Rp 30 juta
            ],
            [
                'account_name' => 'BNI - Investasi',
                'account_number' => '5555666677',
                'bank_name' => 'Bank Negara Indonesia',
                'branch' => 'Jakarta Barat',
                'initial_balance' => 100000000, // Rp 100 juta
            ],
        ];

        foreach ($accounts as $account) {
            BankAccount::create($account);
        }

        $this->command->info('✓ Seeded bank_accounts table: 3 accounts (total initial balance: Rp 180M)');
    }
}
