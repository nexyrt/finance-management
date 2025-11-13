<?php

use App\Livewire\Accounts\Index as AccountsIndex;
use App\Livewire\Admin\RoleManagement;
use App\Livewire\CashFlow\Index as CashFlowIndex;
use App\Livewire\Clients\Index as ClientsIndex;
use App\Livewire\Dashboard;
// Livewire Components
use App\Livewire\Invoices\Index as InvoicesIndex;
use App\Livewire\Invoices\Create as InvoicesCreate;
use App\Livewire\Invoices\Edit as InvoicesEdit;
use App\Livewire\RecurringInvoices\Index as RecurringInvoicesIndex;
use App\Livewire\RecurringInvoices\CreateTemplate as RecurringInvoicesCreateTemplate;
use App\Livewire\RecurringInvoices\EditTemplate as RecurringInvoicesEditTemplate;
use App\Livewire\Reimbursements\Index as ReimbursementIndex;
use App\Livewire\Services\Index as ServicesIndex;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\TestingPage;
use App\Livewire\Transactions\Index as TransactionsIndex;
use App\Livewire\TransactionsCategories\Index as TransactionsCategoriesIndex;
use App\Models\Invoice;
use App\Services\InvoicePrintService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/clients', ClientsIndex::class)->name('clients');
    Route::get('/services', ServicesIndex::class)->name('services');
    Route::get('/bank-accounts', AccountsIndex::class)->name('bank-accounts.index');
    Route::get('/transactions', TransactionsIndex::class)->name('transactions.index');
    Route::get('/transaction-categories', TransactionsCategoriesIndex::class)->name('transaction-categories.index');

    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', InvoicesIndex::class)->name('index');
        Route::get('/create', InvoicesCreate::class)->name('create');
        Route::get('/{invoice}/edit', InvoicesEdit::class)->name('edit');
    });
    
    Route::prefix('recurring-invoices')->name('recurring-invoices.')->group(function () {
        Route::get('/', RecurringInvoicesIndex::class)->name('index');
        Route::get('/template/create', RecurringInvoicesCreateTemplate::class)->name('template.create');
        Route::get('/template/{template}/edit', RecurringInvoicesEditTemplate::class)->name('template.edit');
    });

    Route::get('/cash-flow', CashFlowIndex::class)->name('cash-flow.index');
    Route::get('/reimbursements', ReimbursementIndex::class)->middleware('can:view reimbursements')->name('reimbursements.index');

    // Invoice PDF Operations
    Route::prefix('invoice')->name('invoice.')->group(function () {
        Route::get('/{invoice}/download', function (Invoice $invoice, Request $request) {
            $service = new InvoicePrintService;

            $dpAmount = $request->query('dp_amount') ? (int) $request->query('dp_amount') : null;
            $pelunasanAmount = $request->query('pelunasan_amount') ? (int) $request->query('pelunasan_amount') : null;

            $pdf = $service->generateSingleInvoicePdf($invoice, $dpAmount, $pelunasanAmount);

            $invoiceType = $dpAmount ? 'DP-' : ($pelunasanAmount ? 'Pelunasan-' : '');
            $filename = $invoiceType . 'Invoice-' . str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $invoice->invoice_number) . '.pdf';

            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, $filename, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        })->name('download');

        Route::get('/{invoice}/preview', function (Invoice $invoice, Request $request) {
            $service = new InvoicePrintService;

            $dpAmount = $request->query('dp_amount') ? (int) $request->query('dp_amount') : null;
            $pelunasanAmount = $request->query('pelunasan_amount') ? (int) $request->query('pelunasan_amount') : null;

            $pdf = $service->generateSingleInvoicePdf($invoice, $dpAmount, $pelunasanAmount);

            return response($pdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline',
            ]);
        })->name('preview');
    });

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::redirect('/', '/settings/profile');
        Route::get('/profile', Profile::class)->name('profile');
        Route::get('/password', Password::class)->name('password');
        Route::get('/appearance', Appearance::class)->name('appearance');
    });

    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/roles', RoleManagement::class)->name('roles');
        Route::get('/users', \App\Livewire\Users\Index::class)->name('users');
    });

    Route::get('/test', TestingPage::class)->name('test');
});

require __DIR__ . '/auth.php';
