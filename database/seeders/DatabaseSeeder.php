<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Seeder naming convention: 1 Seeder = 1 Table
     * This makes it clear which tables are being seeded
     */
    public function run(): void
    {
        // 1. Create default admin user (inline - simple & rarely changes)
        $this->seedDefaultUser();

        // 2. Master data seeders (run first - required by other seeders)
        $this->call([
            CompanyProfileSeeder::class,       // Table: company_profiles
            MasterPermissionSeeder::class,     // Tables: roles, permissions, role_has_permissions
            TransactionCategorySeeder::class,  // Table: transaction_categories
        ]);

        // 3. Test data seeders (tables with transactional data)
        $this->call([
            ClientSeeder::class,               // Table: clients
            BankAccountSeeder::class,          // Table: bank_accounts
            InvoiceSeeder::class,              // Tables: invoices, invoice_items, payments
            BankTransactionSeeder::class,      // Table: bank_transactions
        ]);
    }

    /**
     * Create default admin user for login
     */
    private function seedDefaultUser(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        $this->command->info('✓ Created admin user: admin@gmail.com / password');
    }
}
