<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\ClientRelationship;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        // Create individual clients
        $individuals = Client::factory()->individual()->count(10)->create();
        
        // Create company clients
        $companies = Client::factory()->company()->count(15)->create();
        
        // Create relationships (individuals owning companies)
        foreach ($companies as $company) {
            // Some companies have multiple owners
            $ownerCount = rand(1, 3);
            $owners = $individuals->random($ownerCount);
            
            foreach ($owners as $owner) {
                ClientRelationship::create([
                    'owner_id' => $owner->id,
                    'company_id' => $company->id
                ]);
            }
        }
    }
}
