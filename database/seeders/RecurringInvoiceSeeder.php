<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RecurringInvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $templates = DB::table('recurring_templates')->where('status', 'active')->get();

        foreach ($templates as $template) {
            $startDate = new \DateTime($template->start_date);
            $endDate = new \DateTime($template->end_date);
            $currentDate = clone $startDate;

            while ($currentDate <= $endDate) {
                // Skip if date is in the past
                if ($currentDate >= new \DateTime('2025-01-01')) {
                    DB::table('recurring_invoices')->insert([
                        'template_id' => $template->id,
                        'client_id' => $template->client_id,
                        'scheduled_date' => $currentDate->format('Y-m-d'),
                        'invoice_data' => $template->invoice_template,
                        'status' => $currentDate->format('Y-m') === '2025-01' ? 'published' : 'draft',
                        'published_invoice_id' => null, // Will be filled when actually published
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // Increment based on frequency
                switch ($template->frequency) {
                    case 'monthly':
                        $currentDate->modify('+1 month');
                        break;
                    case 'quarterly':
                        $currentDate->modify('+3 months');
                        break;
                    case 'semi_annual':
                        $currentDate->modify('+6 months');
                        break;
                    case 'annual':
                        $currentDate->modify('+1 year');
                        break;
                }
            }
        }
    }
}
