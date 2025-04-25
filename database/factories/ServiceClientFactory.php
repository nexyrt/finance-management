<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Service;
use App\Models\ServiceClient;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceClientFactory extends Factory
{
    protected $model = ServiceClient::class;

    public function definition(): array
    {
        $service = Service::inRandomOrder()->first() ?? Service::factory()->create();
        $price = $service->price;
        
        // Apply a small variation to the standard price
        $amount = $price * (1 + $this->faker->randomFloat(2, -0.10, 0.20));
        
        return [
            'service_id' => $service->id,
            'client_id' => Client::inRandomOrder()->first() ?? Client::factory()->create(),
            'service_date' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'amount' => round($amount, 2),
        ];
    }
}
