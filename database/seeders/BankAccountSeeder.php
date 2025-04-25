<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use Illuminate\Database\Seeder;

class BankAccountSeeder extends Seeder
{
    public function run(): void
    {
        // Create some standard bank accounts with Rupiah currency
        $accounts = [
            [
                'account_name' => 'Rekening Operasional',
                'account_number' => '1234567890',
                'bank_name' => 'Bank Central Asia (BCA)',
                'branch' => 'KCP Sudirman',
                'currency' => 'IDR',
                'initial_balance' => 150000000,
                'current_balance' => 150000000,
            ],
            [
                'account_name' => 'Rekening Tabungan',
                'account_number' => '0987654321',
                'bank_name' => 'Bank Mandiri',
                'branch' => 'KCP Menteng',
                'currency' => 'IDR',
                'initial_balance' => 75000000,
                'current_balance' => 75000000,
            ],
            [
                'account_name' => 'Rekening Gaji',
                'account_number' => '5678901234',
                'bank_name' => 'Bank Negara Indonesia (BNI)',
                'branch' => 'KCP Thamrin',
                'currency' => 'IDR',
                'initial_balance' => 50000000,
                'current_balance' => 50000000,
            ],
            [
                'account_name' => 'Rekening Deposito',
                'account_number' => '1122334455',
                'bank_name' => 'Bank Rakyat Indonesia (BRI)',
                'branch' => 'KCP Gatot Subroto',
                'currency' => 'IDR',
                'initial_balance' => 200000000,
                'current_balance' => 200000000,
            ],
            [
                'account_name' => 'Rekening Dolar',
                'account_number' => 'USD12345678',
                'bank_name' => 'HSBC Indonesia',
                'branch' => 'Jakarta',
                'currency' => 'USD',
                'initial_balance' => 25000,
                'current_balance' => 25000,
            ]
        ];
        
        foreach ($accounts as $account) {
            BankAccount::create($account);
        }
    }
}
