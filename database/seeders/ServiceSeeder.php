<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        // Create services using factory
        Service::factory()->count(20)->create();
        
        $this->command->info('Created 20 services');
    }
}
