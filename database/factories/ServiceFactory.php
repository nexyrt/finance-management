<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true) . ' ' . $this->faker->randomElement(['Konsultasi', 'Pendampingan', 'Layanan']),
            'price' => $this->faker->numberBetween(1500000, 25000000),
            'type' => $this->faker->randomElement(['Perizinan', 'Administrasi Perpajakan', 'Digital Marketing', 'Sistem Digital']),
        ];
    }
}
