<?php

use App\Livewire\BankAccounts;
use App\Livewire\Clients\Index as Clients;
use App\Livewire\Invoices\Index as InvoicesIndex;
use App\Livewire\Invoices\Edit as EditInvoice;
use App\Livewire\Dashboard;
use App\Livewire\ServiceManagement;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\TestingPage;
use App\Models\Invoice;
use Illuminate\Support\Facades\Route;
use Barryvdh\DomPDF\Facade\Pdf;

Route::redirect('/', '/login')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    // Master Data
    Route::get('dashboard', Dashboard::class)->name('dashboard');
    Route::get('/clients', Clients::class)->name('clients');
    Route::get('/services', ServiceManagement::class)->name('services');
    Route::get('/bank-accounts', BankAccounts::class)->name('bank-accounts');

    // Features
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', InvoicesIndex::class)->name('index');
        Route::get('/{invoice}/edit', EditInvoice::class)->name('edit');
    });

    // ✅ PDF PRINT dengan filename sanitization
    Route::get('/invoices/{invoice}/print', function (Invoice $invoice) {
        // Load relationships
        $invoice->load(['client', 'items.client', 'payments.bankAccount']);

        // Company info
        $company = [
            'name' => 'Finance Management System',
            'address' => 'Jl. Contoh No. 123, Jakarta',
            'phone' => '+62 21 1234 5678',
            'email' => 'info@finance.com',
            'website' => 'www.finance.com',
        ];

        // Prepare data
        $data = [
            'invoice' => $invoice,
            'client' => $invoice->client,
            'items' => $invoice->items,
            'payments' => $invoice->payments,
            'options' => [
                'show_payments' => true,
                'show_client_details' => true,
                'notes' => '',
            ],
            'company' => $company,
        ];

        // Generate PDF
        $pdf = Pdf::loadView('pdf.invoice', $data)
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'dpi' => 150,
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
            ]);

        // ✅ SANITIZE filename - ganti karakter invalid
        $safeFilename = 'Invoice-' . str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $invoice->invoice_number) . '.pdf';

        return $pdf->download($safeFilename);
    })->name('invoices.print');

    Route::get('/testing/{invoiceId?}', TestingPage::class)->name('test');
});

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');
    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
});

require __DIR__ . '/auth.php';