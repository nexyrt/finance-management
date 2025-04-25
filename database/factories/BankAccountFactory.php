<?php

namespace Database\Factories;

use App\Models\BankAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

class BankAccountFactory extends Factory
{
    protected $model = BankAccount::class;

    public function definition(): array
    {
        $initialBalance = $this->faker->numberBetween(5000, 50000);
        
        return [
            'account_name' => $this->faker->words(2, true) . ' Account',
            'account_number' => $this->faker->numerify('############'),
            'bank_name' => $this->faker->company() . ' Bank',
            'branch' => $this->faker->city() . ' Branch',
            'currency' => $this->faker->randomElement(['USD', 'EUR', 'GBP']),
            'initial_balance' => $initialBalance,
            'current_balance' => $initialBalance,
        ];
    }
}
