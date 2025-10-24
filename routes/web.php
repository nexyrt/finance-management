<?php

use App\Livewire\Admin\RoleManagement;
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
use App\Livewire\CashFlow\Index as CashFlowIndex;
use App\Livewire\TransactionsCategories\Index as TransactionsCategoriesIndex;
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

    Route::get('/transaction-categories', TransactionsCategoriesIndex::class)
        ->name('transaction-categories.index');

    // Invoice Management
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', InvoicesIndex::class)->name('index');
        Route::get('/{invoice}/edit', InvoicesEdit::class)->name('edit');
    });

    // Recurring Invoice Management
    Route::get('/recurring-invoices', RecurringInvoicesIndex::class)->name('recurring-invoices.index');

    // Cash Flow Management
    Route::get('/cash-flow', CashFlowIndex::class)->name('cash-flow.index');

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

    // Role & Permission Management - Admin only
    // User Management - Admin only
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/roles', RoleManagement::class)->name('roles');
        Route::get('/users', \App\Livewire\Users\Index::class)->name('users');
    });

    // Development Routes (Remove in production)
    Route::get('/test', TestingPage::class)->name('test');
});

require __DIR__ . '/auth.php';