<?php

use App\Livewire\Dashboard;
use App\Livewire\TestingPage;
use App\Models\Invoice;
use App\Services\InvoicePrintService;
use Illuminate\Support\Facades\Route;

// Livewire Components
use App\Livewire\Clients\Index as ClientsIndex;
use App\Livewire\Services\Index as ServicesIndex;
use App\Livewire\Accounts\Index as AccountsIndex;
use App\Livewire\Transactions\Index as TransactionsIndex;
use App\Livewire\Invoices\Index as InvoicesIndex;
use App\Livewire\Invoices\Edit as InvoicesEdit;
use App\Livewire\RecurringInvoices\Index as RecurringInvoicesIndex;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Appearance;

Route::redirect('/', '/login')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // Client Management
    Route::get('/clients', ClientsIndex::class)->name('clients');

    // Service Management
    Route::get('/services', ServicesIndex::class)->name('services');

    // Bank Account Management
    Route::get('/bank-accounts', AccountsIndex::class)->name('bank-accounts.index');

    // Transaction Management
    Route::get('/transactions', TransactionsIndex::class)->name('transactions.index');

    // Invoice Management
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', InvoicesIndex::class)->name('index');
        Route::get('/{invoice}/edit', InvoicesEdit::class)->name('edit');
    });

    // Recurring Invoice Management
    Route::get('/recurring-invoices', RecurringInvoicesIndex::class)->name('recurring-invoices.index');

    // Invoice PDF Operations
    Route::prefix('invoice')->name('invoice.')->group(function () {
        Route::get('/{invoice}/download', function (Invoice $invoice) {
            $service = new InvoicePrintService();
            $pdf = $service->generateSingleInvoicePdf($invoice);
            $filename = 'Invoice-' . str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $invoice->invoice_number) . '.pdf';

            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, $filename, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);
        })->name('download');

        Route::get('/{invoice}/preview', function (Invoice $invoice) {
            $service = new InvoicePrintService();
            $pdf = $service->generateSingleInvoicePdf($invoice);

            return response($pdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline'
            ]);
        })->name('preview');
    });

    // Settings Management
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::redirect('/', '/settings/profile');
        Route::get('/profile', Profile::class)->name('profile');
        Route::get('/password', Password::class)->name('password');
        Route::get('/appearance', Appearance::class)->name('appearance');
    });

    // Development Routes (Remove in production)
    Route::get('/test', TestingPage::class)->name('test');
});

require __DIR__ . '/auth.php';