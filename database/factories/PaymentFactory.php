<?php

namespace Database\Factories;

use App\Models\BankAccount;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'invoice_id' => Invoice::factory(),
            'bank_account_id' => BankAccount::factory(),
            'amount' => $this->faker->randomFloat(2, 1000000, 20000000),
            'payment_date' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'payment_method' => $this->faker->randomElement(['cash', 'bank_transfer']),
            'reference_number' => $this->faker->optional()->numerify('REF#######'),
        ];
    }
}
