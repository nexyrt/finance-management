<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Service;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $allClients = Client::all();
        $services = Service::all();
        $bankAccounts = BankAccount::all();

        for ($i = 1; $i <= 20; $i++) {
            // Create invoice with discount logic
            $discountType = rand(1, 100) <= 30 ? (rand(1, 2) == 1 ? 'percentage' : 'fixed') : 'fixed';
            $discountValue = 0;
            $discountReason = null;

            // Apply discount (30% chance)
            if (rand(1, 100) <= 30) {
                if ($discountType === 'percentage') {
                    $discountValue = rand(500, 2000); // 5% - 20% (stored as basis points)
                    $discountReason = 'Diskon pelanggan setia';
                } else {
                    $discountValue = rand(100000, 1000000); // Rp 100k - 1M
                    $discountReason = 'Diskon khusus';
                }
            }

            $invoice = Invoice::factory()->create([
                'invoice_number' => 'INV-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'billed_to_id' => $allClients->random()->id,
                'subtotal' => 0,
                'discount_amount' => 0,
                'discount_type' => $discountType,
                'discount_value' => $discountValue,
                'discount_reason' => $discountReason,
                'total_amount' => 0, // Will be calculated later
            ]);

            // Create invoice items with BigInt amounts
            $itemCount = rand(1, 4);
            $subtotal = 0;

            for ($j = 0; $j < $itemCount; $j++) {
                $service = $services->random();
                $quantity = rand(1, 5);
                $unitPrice = (int) $service->price + rand(-500000, 1000000); // Convert to integer
                $amount = $quantity * $unitPrice;
                
                // Random COGS (40-80% of unit price)
                $cogsAmount = (int) ($amount * rand(40, 80) / 100);

                InvoiceItem::factory()->create([
                    'invoice_id' => $invoice->id,
                    'client_id' => $allClients->random()->id,
                    'service_name' => $service->name,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'amount' => $amount,
                    'cogs_amount' => $cogsAmount,
                ]);

                $subtotal += $amount;
            }

            // Calculate discount amount and total manually
            $discountAmount = 0;
            if ($discountValue > 0) {
                if ($discountType === 'percentage') {
                    // Convert basis points to percentage (1500 = 15%)
                    $discountAmount = (int) ($subtotal * $discountValue / 10000);
                } else {
                    // Fixed amount discount
                    $discountAmount = min($discountValue, $subtotal); // Don't exceed subtotal
                }
            }

            $totalAmount = $subtotal - $discountAmount;

            // Update invoice with calculated values
            $invoice->update([
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
            ]);

            // Create payments (70% chance)
            if (rand(1, 100) <= 70) {
                $paymentAmount = rand(1, 100) <= 80
                    ? $totalAmount // Full payment
                    : (int) ($totalAmount * rand(30, 90) / 100); // Partial payment

                Payment::factory()->create([
                    'invoice_id' => $invoice->id,
                    'bank_account_id' => $bankAccounts->random()->id,
                    'amount' => $paymentAmount,
                    'payment_date' => $invoice->issue_date->addDays(rand(1, 30)),
                    'reference_number' => 'PAY-' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT),
                ]);
            }

            // Update invoice status using model method
            $invoice->refresh();
            $invoice->updateStatus();
        }
    }
}