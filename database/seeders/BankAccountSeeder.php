<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use Illuminate\Database\Seeder;

class BankAccountSeeder extends Seeder
{
    public function run(): void
    {
        // Create bank accounts using factory
        BankAccount::factory()->count(5)->create();
        
        $this->command->info('Created 5 bank accounts');
    }
}
