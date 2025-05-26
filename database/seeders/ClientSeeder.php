<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\ClientRelationship;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
       // Use your ClientFactory
        Client::factory()->count(10)->individual()->active()->create();
        Client::factory()->count(15)->company()->active()->create();
        Client::factory()->count(3)->inactive()->create();
    }
}
