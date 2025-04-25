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
        $issueDate = $this->faker->dateTimeBetween('-3 months', 'now');
        $dueDate = clone $issueDate;
        $dueDate->modify('+30 days');
        
        return [
            'invoice_number' => 'INV-' . $this->faker->unique()->numerify('#####'),
            'billed_to_id' => Client::inRandomOrder()->first() ?? Client::factory()->create(),
            'total_amount' => 0, // Will be updated when invoice items are added
            'issue_date' => $issueDate,
            'due_date' => $dueDate,
            'status' => $this->faker->randomElement(['draft', 'sent', 'paid', 'partially_paid', 'overdue']),
            'payment_terms' => $this->faker->randomElement(['full', 'installment']),
            'installment_count' => function (array $attributes) {
                return $attributes['payment_terms'] === 'installment' 
                    ? $this->faker->numberBetween(2, 6) 
                    : 1;
            },
        ];
    }
}
