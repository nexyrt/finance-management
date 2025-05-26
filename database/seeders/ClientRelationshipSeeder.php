<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\ClientRelationship;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientRelationshipSeeder extends Seeder
{
    public function run(): void
    {
        $individuals = Client::where('type', 'individual')->take(5)->get();
        $companies = Client::where('type', 'company')->take(8)->get();

        foreach ($individuals as $individual) {
            $ownedCompanies = $companies->random(rand(1, 3));

            foreach ($ownedCompanies as $company) {
                ClientRelationship::firstOrCreate([
                    'owner_id' => $individual->id,
                    'company_id' => $company->id,
                ]);
            }
        }
    }
}
