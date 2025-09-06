<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

class RecurringTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $clients = Client::where('status', 'Active')->take(5)->get();
        $services = Service::all();

        foreach ($clients as $client) {
            DB::table('recurring_templates')->insert([
                'client_id' => $client->id,
                'template_name' => 'Monthly Service - ' . $client->name,
                'start_date' => '2025-01-01',
                'end_date' => '2025-12-31',
                'frequency' => 'monthly',
                'next_generation_date' => '2025-01-01',
                'status' => 'active',
                'invoice_template' => json_encode([
                    'subtotal' => 5000000,
                    'discount_amount' => 0,
                    'discount_type' => 'fixed',
                    'discount_value' => 0,
                    'discount_reason' => null,
                    'total_amount' => 5000000,
                    'items' => [
                        [
                            'service_name' => $services->random()->name,
                            'quantity' => 1,
                            'unit_price' => 5000000,
                            'amount' => 5000000,
                            'cogs_amount' => 2000000
                        ]
                    ]
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Additional quarterly template
        if ($clients->count() > 0) {
            DB::table('recurring_templates')->insert([
                'client_id' => $clients->first()->id,
                'template_name' => 'Quarterly Maintenance - ' . $clients->first()->name,
                'start_date' => '2025-01-01',
                'end_date' => '2026-01-01',
                'frequency' => 'quarterly',
                'next_generation_date' => '2025-01-01',
                'status' => 'active',
                'invoice_template' => json_encode([
                    'subtotal' => 15000000,
                    'discount_amount' => 1500000,
                    'discount_type' => 'fixed',
                    'discount_value' => 1500000,
                    'discount_reason' => 'Quarterly discount',
                    'total_amount' => 13500000,
                    'items' => [
                        [
                            'service_name' => 'System Maintenance',
                            'quantity' => 3,
                            'unit_price' => 5000000,
                            'amount' => 15000000,
                            'cogs_amount' => 6000000
                        ]
                    ]
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
