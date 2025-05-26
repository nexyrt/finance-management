<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceItemFactory extends Factory
{
    protected $model = InvoiceItem::class;

    public function definition(): array
    {
        // Get random service name from existing services or create new
        $serviceNames = [
            'Konsultasi Pajak',
            'Website Development',
            'Social Media Management',
            'Pengurusan SIUP',
            'SEO Optimization',
            'Mobile App Development',
        ];

        return [
            'invoice_id' => Invoice::factory(),
            'client_id' => Client::factory(),
            'service_name' => $this->faker->randomElement($serviceNames),
            'amount' => $this->faker->randomFloat(2, 500000, 10000000),
        ];
    }
}
