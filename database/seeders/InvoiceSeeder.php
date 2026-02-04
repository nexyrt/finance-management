<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    /**
     * Seed the invoices, invoice_items, and payments tables
     *
     * Creates test data for 3 months (Jan-Mar 2026) with simple, verifiable numbers
     */
    public function run(): void
    {
        $clients = Client::all();
        $bankAccount = BankAccount::first();

        if ($clients->isEmpty() || !$bankAccount) {
            $this->command->error('⚠ Please run ClientSeeder and BankAccountSeeder first!');
            return;
        }

        // MONTH 1: Januari 2026 - Rp 3.000.000
        $this->createInvoiceWithPayment(
            client: $clients[0],
            invoiceNumber: 'INV/01/KSN/01.26',
            issueDate: Carbon::create(2026, 1, 5),
            dueDate: Carbon::create(2026, 1, 20),
            items: [
                ['service' => 'Konsultasi IT', 'qty' => 10, 'unit' => 'jam', 'price' => 200000, 'cogs' => 50000],
                ['service' => 'Setup Server', 'qty' => 1, 'unit' => 'project', 'price' => 1000000, 'cogs' => 300000],
            ],
            paymentAmount: 3000000,
            paymentDate: Carbon::create(2026, 1, 15),
            bankAccount: $bankAccount
        );

        // MONTH 2: Februari 2026 - Rp 4.000.000 (2jt + 2jt)
        $invoice2 = $this->createInvoiceWithPayment(
            client: $clients[1],
            invoiceNumber: 'INV/02/KSN/02.26',
            issueDate: Carbon::create(2026, 2, 7),
            dueDate: Carbon::create(2026, 2, 22),
            items: [
                ['service' => 'Website Development', 'qty' => 1, 'unit' => 'project', 'price' => 4000000, 'cogs' => 1500000],
            ],
            paymentAmount: 2000000, // Partial 50%
            paymentDate: Carbon::create(2026, 2, 18),
            bankAccount: $bankAccount
        );

        $this->createInvoiceWithPayment(
            client: $clients[2],
            invoiceNumber: 'INV/03/KSN/02.26',
            issueDate: Carbon::create(2026, 2, 10),
            dueDate: Carbon::create(2026, 2, 25),
            items: [
                ['service' => 'SEO Service', 'qty' => 4, 'unit' => 'bulan', 'price' => 500000, 'cogs' => 100000],
            ],
            paymentAmount: 2000000,
            paymentDate: Carbon::create(2026, 2, 20),
            bankAccount: $bankAccount
        );

        // MONTH 3: Maret 2026 - Rp 2.000.000
        $this->createInvoiceWithPayment(
            client: $clients[0],
            invoiceNumber: 'INV/04/KSN/03.26',
            issueDate: Carbon::create(2026, 3, 5),
            dueDate: Carbon::create(2026, 3, 20),
            items: [
                ['service' => 'Maintenance Bulanan', 'qty' => 2, 'unit' => 'bulan', 'price' => 1000000, 'cogs' => 200000],
            ],
            paymentAmount: 2000000,
            paymentDate: Carbon::create(2026, 3, 18),
            bankAccount: $bankAccount
        );

        $this->command->info('✓ Seeded invoices table: 4 invoices with items and payments');
        $this->command->info('  → Total payments: Rp 9.000.000 (Jan: 3jt, Feb: 4jt, Mar: 2jt)');
    }

    private function createInvoiceWithPayment($client, $invoiceNumber, $issueDate, $dueDate, $items, $paymentAmount, $paymentDate, $bankAccount): Invoice
    {
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += $item['qty'] * $item['price'];
        }

        // Create invoice
        $invoice = Invoice::create([
            'invoice_number' => $invoiceNumber,
            'billed_to_id' => $client->id,
            'subtotal' => $subtotal,
            'discount_amount' => 0,
            'discount_type' => 'fixed',
            'discount_value' => 0,
            'total_amount' => $subtotal,
            'issue_date' => $issueDate,
            'due_date' => $dueDate,
            'status' => 'draft',
        ]);

        // Create invoice items
        foreach ($items as $item) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'client_id' => $client->id,
                'service_name' => $item['service'],
                'quantity' => $item['qty'],
                'unit' => $item['unit'],
                'unit_price' => $item['price'],
                'amount' => $item['qty'] * $item['price'],
                'cogs_amount' => $item['cogs'] * $item['qty'],
                'is_tax_deposit' => false,
            ]);
        }

        // Create payment
        Payment::create([
            'invoice_id' => $invoice->id,
            'bank_account_id' => $bankAccount->id,
            'amount' => $paymentAmount,
            'payment_date' => $paymentDate,
            'payment_method' => 'bank_transfer',
            'reference_number' => 'PAY-' . $paymentDate->format('Ymd') . '-' . $invoice->id,
        ]);

        // Update invoice status
        $invoice->refresh();
        $invoice->updateStatus();

        return $invoice;
    }
}
