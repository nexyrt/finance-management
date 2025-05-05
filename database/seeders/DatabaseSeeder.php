<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            ClientSeeder::class,
            ServiceSeeder::class,
            BankAccountSeeder::class,
            InvoiceSeeder::class,
        ]);
    }
}
