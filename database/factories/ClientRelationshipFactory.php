<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClientRelationship>
 */
class ClientRelationshipFactory extends Factory
{
    protected $model = ClientRelationship::class;

    public function definition(): array
    {
        return [
            'owner_id' => Client::factory()->individual(),
            'company_id' => Client::factory()->company(),
        ];
    }
}
