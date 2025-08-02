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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Barryvdh\DomPDF\Facade\Pdf;

Route::redirect('/', '/login')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // Master Data
    Route::get('/clients', Clients::class)->name('clients');
    Route::get('/services', ServiceManagement::class)->name('services');
    Route::get('/bank-accounts', BankAccounts::class)->name('bank-accounts');

    // Invoice Management
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', InvoicesIndex::class)->name('index');
        Route::get('/{invoice}/edit', EditInvoice::class)->name('edit');
        
        // Invoice Print PDF
        Route::get('/{invoice}/print', function (Invoice $invoice) {
            $invoice->load(['client', 'items.client', 'payments.bankAccount']);

            $company = [
                'name' => 'Finance Management System',
                'address' => 'Jl. Contoh No. 123, Jakarta',
                'phone' => '+62 21 1234 5678',
                'email' => 'info@finance.com',
                'website' => 'www.finance.com',
            ];

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

            $pdf = Pdf::loadView('pdf.invoice', $data)
                ->setPaper('A4', 'portrait')
                ->setOptions([
                    'dpi' => 150,
                    'defaultFont' => 'DejaVu Sans',
                    'isHtml5ParserEnabled' => true,
                    'isPhpEnabled' => true,
                ]);

            $safeFilename = 'Invoice-' . str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $invoice->invoice_number) . '.pdf';

            return $pdf->download($safeFilename);
        })->name('print');
    });

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::redirect('/', 'profile');
        Route::get('/profile', Profile::class)->name('profile');
        Route::get('/password', Password::class)->name('password');
        Route::get('/appearance', Appearance::class)->name('appearance');
    });

    // Testing (Development only)
    Route::get('/testing/{invoiceId?}', TestingPage::class)->name('test');
});

require __DIR__ . '/auth.php';