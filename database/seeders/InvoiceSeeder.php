<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $allClients = Client::all();
        $services = Service::all();
        $bankAccounts = BankAccount::all();

        $invoiceCounter = 1;

        // Generate invoices and payments for each month of 2025
        for ($month = 1; $month <= 12; $month++) {
            $startDate = Carbon::create(2025, $month, 1);
            $endDate = $startDate->copy()->endOfMonth();

            // 5-10 invoices per month
            $invoiceCount = rand(5, 10);

            for ($i = 0; $i < $invoiceCount; $i++) {
                // Random invoice date within the month
                $invoiceDate = $startDate->copy()->addDays(rand(0, $endDate->day - 1));
                $dueDate = $invoiceDate->copy()->addDays(rand(7, 30));

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
                    'invoice_number' => sprintf('INV/%02d/KSN/%02d.25', $invoiceCounter, $month),
                    'billed_to_id' => $allClients->random()->id,
                    'issue_date' => $invoiceDate,
                    'due_date' => $dueDate,
                    'subtotal' => 0,
                    'discount_amount' => 0,
                    'discount_type' => $discountType,
                    'discount_value' => $discountValue,
                    'discount_reason' => $discountReason,
                    'total_amount' => 0,
                    'created_at' => $invoiceDate->copy()->addHours(rand(8, 17)),
                    'updated_at' => $invoiceDate->copy()->addHours(rand(8, 17)),
                ]);

                // Create invoice items
                $itemCount = rand(1, 4);
                $subtotal = 0;

                for ($j = 0; $j < $itemCount; $j++) {
                    $service = $services->random();
                    $quantity = rand(1, 5);
                    $unitPrice = (int) $service->price + rand(-500000, 1000000);
                    $amount = $quantity * $unitPrice;

                    // Random COGS (40-80% of unit price)
                    $cogsAmount = (int) ($amount * rand(40, 80) / 100);

                    // 10% chance for tax deposit
                    $isTaxDeposit = rand(1, 100) <= 10;

                    InvoiceItem::factory()->create([
                        'invoice_id' => $invoice->id,
                        'client_id' => $allClients->random()->id,
                        'service_name' => $isTaxDeposit ? 'Setor Pajak - ' . $service->name : $service->name,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'amount' => $amount,
                        'cogs_amount' => $isTaxDeposit ? 0 : $cogsAmount, // No COGS for tax deposits
                        'is_tax_deposit' => $isTaxDeposit,
                    ]);

                    $subtotal += $amount;
                }

                // Calculate discount amount and total
                $discountAmount = 0;
                if ($discountValue > 0) {
                    if ($discountType === 'percentage') {
                        $discountAmount = (int) ($subtotal * $discountValue / 10000);
                    } else {
                        $discountAmount = min($discountValue, $subtotal);
                    }
                }

                $totalAmount = $subtotal - $discountAmount;

                // Update invoice with calculated values
                $invoice->update([
                    'subtotal' => $subtotal,
                    'discount_amount' => $discountAmount,
                    'total_amount' => $totalAmount,
                ]);

                // Create payments for this month (70% chance)
                if (rand(1, 100) <= 70) {
                    $paymentCount = rand(1, 2); // 1-2 payments per invoice
                    $remainingAmount = $totalAmount;

                    for ($p = 0; $p < $paymentCount && $remainingAmount > 0; $p++) {
                        $isLastPayment = ($p == $paymentCount - 1);

                        if ($isLastPayment) {
                            $paymentAmount = $remainingAmount; // Pay remaining
                        } else {
                            $paymentAmount = (int) ($remainingAmount * rand(30, 70) / 100);
                        }

                        // Payment date within the month, after invoice date
                        $earliestPaymentDate = max($invoiceDate, $startDate);
                        $latestPaymentDate = min($endDate, $invoiceDate->copy()->addDays(30));

                        $paymentDate = $earliestPaymentDate->copy()->addDays(
                            rand(0, max(0, $latestPaymentDate->diffInDays($earliestPaymentDate)))
                        );

                        Payment::factory()->create([
                            'invoice_id' => $invoice->id,
                            'bank_account_id' => $bankAccounts->random()->id,
                            'amount' => $paymentAmount,
                            'payment_date' => $paymentDate,
                            'payment_method' => rand(1, 100) <= 80 ? 'bank_transfer' : 'cash',
                            'reference_number' => 'PAY-' . $paymentDate->format('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                            'created_at' => $paymentDate->copy()->addHours(rand(8, 17)),
                            'updated_at' => $paymentDate->copy()->addHours(rand(8, 17)),
                        ]);

                        $remainingAmount -= $paymentAmount;
                    }
                }

                // Update invoice status
                $invoice->refresh();
                $invoice->updateStatus();

                $invoiceCounter++;
            }

            // Generate additional standalone payments for existing invoices
            $standalonePaymentCount = rand(3, 7);
            for ($sp = 0; $sp < $standalonePaymentCount; $sp++) {
                $paymentDate = $startDate->copy()->addDays(rand(0, $endDate->day - 1));

                // Get random unpaid or partially paid invoice from previous months
                $eligibleInvoices = Invoice::where('created_at', '<', $endDate)
                    ->whereRaw('total_amount > (SELECT COALESCE(SUM(amount), 0) FROM payments WHERE invoice_id = invoices.id)')
                    ->get();

                if ($eligibleInvoices->count() > 0) {
                    $targetInvoice = $eligibleInvoices->random();
                    $paidAmount = $targetInvoice->payments()->sum('amount');
                    $remainingAmount = $targetInvoice->total_amount - $paidAmount;

                    $paymentAmount = min($remainingAmount, (int) ($remainingAmount * rand(30, 100) / 100));

                    Payment::factory()->create([
                        'invoice_id' => $targetInvoice->id,
                        'bank_account_id' => $bankAccounts->random()->id,
                        'amount' => $paymentAmount,
                        'payment_date' => $paymentDate,
                        'payment_method' => rand(1, 100) <= 80 ? 'bank_transfer' : 'cash',
                        'reference_number' => 'PAY-' . $paymentDate->format('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                        'created_at' => $paymentDate->copy()->addHours(rand(8, 17)),
                        'updated_at' => $paymentDate->copy()->addHours(rand(8, 17)),
                    ]);

                    // Update invoice status
                    $targetInvoice->refresh();
                    $targetInvoice->updateStatus();
                }
            }
        }
    }
}