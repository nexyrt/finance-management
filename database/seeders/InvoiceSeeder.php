<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\ServiceClient;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        // Create 10 invoices with items and some with payments
        for ($i = 1; $i <= 10; $i++) {
            // Create invoice
            $invoice = Invoice::factory()->create([
                'invoice_number' => 'INV-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'status' => 'sent', // Start all as sent
                'billed_to_id' => Client::inRandomOrder()->first()->id, // Ensure each invoice has a client
            ]);
            
            // Add 1-5 invoice items
            $itemCount = rand(1, 5);
            $total = 0;
            
            for ($j = 0; $j < $itemCount; $j++) {
                // Get a service client that isn't already invoiced
                $serviceClient = ServiceClient::whereDoesntHave('invoiceItems')->inRandomOrder()->first();
                
                if (!$serviceClient) {
                    // Create a new service client if none available
                    $serviceClient = ServiceClient::factory()->create();
                }
                
                // Create invoice item
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'service_client_id' => $serviceClient->id,
                    'amount' => $serviceClient->amount,
                ]);
                
                $total += $serviceClient->amount;
            }
            
            // Update invoice total
            $invoice->update(['total_amount' => $total]);
            
            // For some invoices, create payment records
            if ($i <= 7) { // 70% of invoices have some payment
                $bankAccount = BankAccount::inRandomOrder()->first();
                
                if ($invoice->payment_terms === 'full') {
                    // For full payment terms, either fully paid or partially paid
                    $paymentAmount = ($i <= 4) ? $total : $total * rand(30, 80) / 100;
                    
                    Payment::create([
                        'invoice_id' => $invoice->id,
                        'bank_account_id' => $bankAccount->id,
                        'amount' => $paymentAmount,
                        'payment_date' => $invoice->issue_date->addDays(rand(1, 20)),
                        'payment_method' => collect(['bank_transfer', 'credit_card', 'check'])->random(),
                        'reference_number' => 'REF-' . strtoupper(substr(md5(rand()), 0, 10)),
                        'installment_number' => 1,
                    ]);
                    
                    // Update invoice status based on payment
                    if ($paymentAmount >= $total) {
                        $invoice->update(['status' => 'paid']);
                    } else {
                        $invoice->update(['status' => 'partially_paid']);
                    }
                } else {
                    // For installment payment terms
                    $installmentAmount = round($total / $invoice->installment_count, 2);
                    $paidInstallments = rand(1, $invoice->installment_count);
                    
                    for ($k = 1; $k <= $paidInstallments; $k++) {
                        Payment::create([
                            'invoice_id' => $invoice->id,
                            'bank_account_id' => $bankAccount->id,
                            'amount' => $installmentAmount,
                            'payment_date' => $invoice->issue_date->addDays($k * 30),
                            'payment_method' => collect(['bank_transfer', 'credit_card', 'check'])->random(),
                            'reference_number' => 'REF-' . strtoupper(substr(md5(rand()), 0, 10)),
                            'installment_number' => $k,
                        ]);
                    }
                    
                    // Update invoice status based on installments paid
                    if ($paidInstallments >= $invoice->installment_count) {
                        $invoice->update(['status' => 'paid']);
                    } else {
                        $invoice->update(['status' => 'partially_paid']);
                    }
                }
            } else if ($i > 7 && $i <= 9) {
                // Some invoices are overdue
                $invoice->update([
                    'status' => 'overdue',
                    'issue_date' => now()->subDays(45),
                    'due_date' => now()->subDays(15),
                ]);
            }
            // The remaining invoices stay as "sent"
        }
    }
}
