<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'type' => $this->faker->randomElement(['individual', 'company']),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'tax_id' => $this->faker->numerify('TAX-########'),
        ];
    }

    public function individual()
    {
        return $this->state(function () {
            return [
                'name' => $this->faker->name(),
                'type' => 'individual',
                'tax_id' => $this->faker->numerify('ID-########'),
            ];
        });
    }

    public function company()
    {
        return $this->state(function () {
            return [
                'name' => $this->faker->company(),
                'type' => 'company',
                'tax_id' => $this->faker->numerify('COM-########'),
            ];
        });
    }
}
