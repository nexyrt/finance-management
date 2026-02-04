<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\TransactionCategory;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BankTransactionSeeder extends Seeder
{
    /**
     * Seed the bank_transactions table
     *
     * Creates test data for 3 months (Jan-Mar 2026)
     * - Income (credit): Rp 5.000.000
     * - Expense (debit): Rp 9.000.000
     */
    public function run(): void
    {
        $bankAccount = BankAccount::first();
        $incomeCategory = TransactionCategory::where('type', 'income')->first();
        $expenseCategories = TransactionCategory::where('type', 'expense')
            ->whereNotNull('parent_code')
            ->limit(5)
            ->get();

        if (!$bankAccount || !$incomeCategory || $expenseCategories->isEmpty()) {
            $this->command->error('⚠ Please run BankAccountSeeder and TransactionCategorySeeder first!');
            return;
        }

        // === INCOME (CREDIT) - Rp 5.000.000 ===

        BankTransaction::create([
            'bank_account_id' => $bankAccount->id,
            'category_id' => $incomeCategory->id,
            'amount' => 2000000,
            'transaction_date' => Carbon::create(2026, 1, 10),
            'transaction_type' => 'credit',
            'description' => 'Bonus Project dari Client Lama',
            'reference_number' => 'TRX-20260110-001',
        ]);

        BankTransaction::create([
            'bank_account_id' => $bankAccount->id,
            'category_id' => $incomeCategory->id,
            'amount' => 2000000,
            'transaction_date' => Carbon::create(2026, 2, 12),
            'transaction_type' => 'credit',
            'description' => 'Bunga Deposito Bank',
            'reference_number' => 'TRX-20260212-002',
        ]);

        BankTransaction::create([
            'bank_account_id' => $bankAccount->id,
            'category_id' => $incomeCategory->id,
            'amount' => 1000000,
            'transaction_date' => Carbon::create(2026, 3, 8),
            'transaction_type' => 'credit',
            'description' => 'Refund Pajak',
            'reference_number' => 'TRX-20260308-003',
        ]);

        // === EXPENSE (DEBIT) - Rp 9.000.000 ===

        // Januari
        BankTransaction::create([
            'bank_account_id' => $bankAccount->id,
            'category_id' => $expenseCategories[0]->id,
            'amount' => 2000000,
            'transaction_date' => Carbon::create(2026, 1, 25),
            'transaction_type' => 'debit',
            'description' => 'Gaji Karyawan - Januari',
            'reference_number' => 'TRX-20260125-004',
        ]);

        BankTransaction::create([
            'bank_account_id' => $bankAccount->id,
            'category_id' => $expenseCategories[1]->id,
            'amount' => 1000000,
            'transaction_date' => Carbon::create(2026, 1, 3),
            'transaction_type' => 'debit',
            'description' => 'Sewa Kantor - Januari',
            'reference_number' => 'TRX-20260103-005',
        ]);

        // Februari
        BankTransaction::create([
            'bank_account_id' => $bankAccount->id,
            'category_id' => $expenseCategories[0]->id,
            'amount' => 2000000,
            'transaction_date' => Carbon::create(2026, 2, 25),
            'transaction_type' => 'debit',
            'description' => 'Gaji Karyawan - Februari',
            'reference_number' => 'TRX-20260225-006',
        ]);

        BankTransaction::create([
            'bank_account_id' => $bankAccount->id,
            'category_id' => $expenseCategories[1]->id,
            'amount' => 1000000,
            'transaction_date' => Carbon::create(2026, 2, 3),
            'transaction_type' => 'debit',
            'description' => 'Sewa Kantor - Februari',
            'reference_number' => 'TRX-20260203-007',
        ]);

        BankTransaction::create([
            'bank_account_id' => $bankAccount->id,
            'category_id' => $expenseCategories[2]->id,
            'amount' => 500000,
            'transaction_date' => Carbon::create(2026, 2, 8),
            'transaction_type' => 'debit',
            'description' => 'Internet & Listrik',
            'reference_number' => 'TRX-20260208-008',
        ]);

        BankTransaction::create([
            'bank_account_id' => $bankAccount->id,
            'category_id' => $expenseCategories[3]->id,
            'amount' => 500000,
            'transaction_date' => Carbon::create(2026, 2, 15),
            'transaction_type' => 'debit',
            'description' => 'Iklan Google Ads',
            'reference_number' => 'TRX-20260215-009',
        ]);

        // Maret
        BankTransaction::create([
            'bank_account_id' => $bankAccount->id,
            'category_id' => $expenseCategories[0]->id,
            'amount' => 2000000,
            'transaction_date' => Carbon::create(2026, 3, 25),
            'transaction_type' => 'debit',
            'description' => 'Gaji Karyawan - Maret',
            'reference_number' => 'TRX-20260325-010',
        ]);

        $this->command->info('✓ Seeded bank_transactions table: 10 transactions');
        $this->command->info('  → Income (credit): Rp 5.000.000');
        $this->command->info('  → Expense (debit): Rp 9.000.000');
    }
}
