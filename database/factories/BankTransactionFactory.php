<?php

namespace Database\Factories;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BankTransaction>
 */
class BankTransactionFactory extends Factory
{
    protected $model = BankTransaction::class;

    public function definition(): array
    {
        return [
            'bank_account_id' => BankAccount::factory(),
            'amount' => $this->faker->randomFloat(2, 100000, 50000000),
            'transaction_date' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'transaction_type' => $this->faker->randomElement(['debit', 'credit']),
            'description' => $this->faker->sentence,
            'reference_number' => $this->faker->optional()->numerify('TRX#######'),
        ];
    }

    public function credit()
    {
        return $this->state(fn (array $attributes) => [
            'transaction_type' => 'credit',
            'description' => 'Payment received',
        ]);
    }

    public function debit()
    {
        return $this->state(fn (array $attributes) => [
            'transaction_type' => 'debit',
            'description' => 'Business expense',
        ]);
    }
}
