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
            // Use your InvoiceFactory
            $invoice = Invoice::factory()->create([
                'invoice_number' => 'INV-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'billed_to_id' => $allClients->random()->id,
            ]);

            // Use your InvoiceItemFactory
            $itemCount = rand(1, 4);
            $totalAmount = 0;

            for ($j = 0; $j < $itemCount; $j++) {
                $service = $services->random();
                $amount = $service->price + rand(-500000, 1000000);

                InvoiceItem::factory()->create([
                    'invoice_id' => $invoice->id,
                    'client_id' => $allClients->random()->id,
                    'service_name' => $service->name,
                    'amount' => $amount,
                ]);

                $totalAmount += $amount;
            }

            $invoice->update(['total_amount' => $totalAmount]);

            // Use your PaymentFactory (70% chance)
            if (rand(1, 100) <= 70) {
                $paymentAmount = rand(1, 100) <= 80
                    ? $totalAmount
                    : $totalAmount * rand(30, 90) / 100;

                Payment::factory()->create([
                    'invoice_id' => $invoice->id,
                    'bank_account_id' => $bankAccounts->random()->id,
                    'amount' => $paymentAmount,
                    'payment_date' => $invoice->issue_date->addDays(rand(1, 30)),
                    'reference_number' => 'PAY-' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT),
                ]);
            }

            // Update invoice status
            $invoice->refresh();
            $amountPaid = $invoice->payments->sum('amount');

            if ($amountPaid >= $invoice->total_amount) {
                $invoice->update(['status' => 'paid']);
            } elseif ($amountPaid > 0) {
                $invoice->update(['status' => 'partially_paid']);
            } else {
                $invoice->update(['status' => rand(1, 100) <= 60 ? 'sent' : 'draft']);
            }
        }
    }
}
