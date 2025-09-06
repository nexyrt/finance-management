<?php

use App\Livewire\Dashboard;
use App\Livewire\Clients\Index as Clients;
use App\Livewire\Invoices\Index as InvoicesIndex;
use App\Livewire\Invoices\Edit as EditInvoice;
use App\Livewire\RecurringInvoices\Index as RecurringInvoicesIndex;
use App\Livewire\Accounts\Index as BankAccountsIndex;
use App\Livewire\Transactions\Index as TransactionsIndex;
use App\Livewire\ServiceManagement;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\TestingPage;
use App\Models\Invoice;
use App\Services\InvoicePrintService;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // Master Data
    Route::get('/clients', Clients::class)->name('clients');
    Route::get('/services', ServiceManagement::class)->name('services');

    // Bank Accounts Management
    Route::prefix('bank-accounts')->name('bank-accounts.')->group(function () {
        Route::get('/', BankAccountsIndex::class)->name('index');
    });

    // Transactions Management
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', TransactionsIndex::class)->name('index');
    });

    // Invoice Management
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', InvoicesIndex::class)->name('index');
        Route::get('/{invoice}/edit', EditInvoice::class)->name('edit');
    });

    // Recurring Invoice Management
    Route::prefix('recurring-invoices')->name('recurring-invoices.')->group(function () {
        Route::get('/', RecurringInvoicesIndex::class)->name('index');
    });

    Route::get('test', TestingPage::class)->name('test');

    // Route untuk direct PDF download (bulk print)
    Route::get('/invoice/{invoice}/download', function (Invoice $invoice) {
        $service = new InvoicePrintService();
        $pdf = $service->generateSingleInvoicePdf($invoice);

        $filename = 'Invoice-' . str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $invoice->invoice_number) . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    })->name('invoice.pdf.download');

    // Route untuk preview (existing)
    Route::get('/invoice/{invoice}/preview', function (Invoice $invoice) {
        $service = new InvoicePrintService();
        $pdf = $service->generateSingleInvoicePdf($invoice);

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline'
        ]);
    })->name('invoice.pdf.preview');

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::redirect('/', 'profile');
        Route::get('/profile', Profile::class)->name('profile');
        Route::get('/password', Password::class)->name('password');
        Route::get('/appearance', Appearance::class)->name('appearance');
    });

    // Testing (Development only)
    Route::get('/test', TestingPage::class)->name('test');
});

require __DIR__ . '/auth.php';