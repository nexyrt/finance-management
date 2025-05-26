<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        return [
            'invoice_number' => 'INV-' . $this->faker->unique()->numerify('######'),
            'billed_to_id' => Client::factory(),
            'total_amount' => $this->faker->randomFloat(2, 1000000, 50000000),
            'issue_date' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'due_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'status' => $this->faker->randomElement(['draft', 'sent', 'paid', 'partially_paid', 'overdue']),
        ];
    }

    public function draft()
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'draft',
        ]);
    }

    public function sent()
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'sent',
        ]);
    }

    public function paid()
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'paid',
        ]);
    }
}
