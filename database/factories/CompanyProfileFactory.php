<?php

namespace Database\Factories;

use App\Models\CompanyProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CompanyProfile>
 */
class CompanyProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'address' => $this->faker->address,
            'email' => $this->faker->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'npwp' => $this->faker->numerify('##.###.###.#-###.###'),
            'is_pkp' => false,
            'finance_manager_name' => $this->faker->name,
            'finance_manager_position' => 'Manajer Keuangan',
        ];
    }
}
