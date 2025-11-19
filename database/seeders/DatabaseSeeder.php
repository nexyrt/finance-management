<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ServiceSeeder::class,
            BankAccountSeeder::class,
            ClientSeeder::class,
            ClientRelationshipSeeder::class,
            InvoiceSeeder::class,
            BankTransactionSeeder::class,
            RecurringTemplateSeeder::class,
            RecurringInvoiceSeeder::class,
            TransactionCategorySeeder::class,
            RolePermissionSeeder::class,
            ReimbursementPermissionSeeder::class,
            CompanyProfileSeeder::class,
        ]);
    }
}
