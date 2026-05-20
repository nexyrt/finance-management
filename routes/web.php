<?php

use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\BankTransactionController;
use App\Http\Controllers\CashFlowController;
use App\Http\Controllers\CashFlowExportController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FundRequestController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReceivableController;
use App\Http\Controllers\RecurringInvoiceController;
use App\Http\Controllers\ReimbursementController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TransactionCategoryController;
use App\Livewire\Feedbacks\Index as FeedbacksIndex;
use App\Livewire\Permissions\Index as PermissionsIndex;
use App\Livewire\Settings\CompanyProfileSettings;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\TestingPage;
use App\Livewire\Users\Index as UsersIndex;
use App\Models\BankAccount;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\TransactionCategory;
use App\Services\FundRequestExportService;
use App\Services\InvoicePrintService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ============================================================================
// PUBLIC ROUTES
// ============================================================================

Route::redirect('/', '/login')->name('home');

// ============================================================================
// AUTHENTICATED ROUTES
// ============================================================================

Route::get('/api/transaction-categories', function (Request $request) {
    $type = $request->input('type');

    $categoryTypes = match ($type) {
        'credit' => ['income', 'adjustment', 'transfer'],
        'debit' => ['expense', 'adjustment', 'transfer'],
        'income' => ['income'],
        'expense' => ['expense'],
        'adjustment' => ['adjustment'],
        'transfer' => ['transfer'],
        default => ['income', 'expense', 'adjustment', 'transfer'],
    };

    return TransactionCategory::whereNull('parent_id')
        ->whereIn('type', $categoryTypes)
        ->with('children')
        ->orderBy('type')
        ->orderBy('label')
        ->get()
        ->flatMap(function ($parent) {
            $items = [];
            $items[] = ['label' => $parent->label, 'value' => $parent->id, 'disabled' => true];
            foreach ($parent->children as $child) {
                $items[] = ['label' => '↳ '.$child->label, 'value' => $child->id];
            }

            return $items;
        })
        ->values();
})->name('api.transaction-categories');

Route::get('/api/bank-accounts', function () {
    return BankAccount::orderBy('bank_name')
        ->orderBy('account_name')
        ->get()
        ->map(fn ($account) => [
            'label' => $account->account_name.' ('.$account->bank_name.')',
            'value' => $account->id,
        ]);
})->name('api.bank-accounts');

Route::get('/api/clients', function () {
    return Client::orderBy('name')
        ->get(['id', 'name'])
        ->map(fn ($client) => [
            'label' => $client->name,
            'value' => $client->id,
        ]);
})->name('api.clients');

Route::middleware(['auth', 'verified'])->group(function () {

    // ------------------------------------------------------------------------
    // DASHBOARD
    // ------------------------------------------------------------------------
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // ------------------------------------------------------------------------
    // CLIENTS & SERVICES
    // ------------------------------------------------------------------------
    Route::middleware('can:view clients')->group(function () {
        Route::get('/clients', [ClientController::class, 'index'])->name('clients');
        Route::post('/clients', [ClientController::class, 'store'])->middleware('can:create clients')->name('clients.store');
        Route::put('/clients/{client}', [ClientController::class, 'update'])->middleware('can:edit clients')->name('clients.update');
        Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->middleware('can:delete clients')->name('clients.destroy');
    });

    Route::middleware('can:view services')->group(function () {
        Route::get('/services', [ServiceController::class, 'index'])->name('services');
        Route::post('/services', [ServiceController::class, 'store'])->middleware('can:create services')->name('services.store');
        Route::put('/services/{service}', [ServiceController::class, 'update'])->middleware('can:edit services')->name('services.update');
        Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->middleware('can:delete services')->name('services.destroy');
    });

    // ------------------------------------------------------------------------
    // FINANCE - INVOICES
    // ------------------------------------------------------------------------
    Route::prefix('invoices')->name('invoices.')->middleware('can:view invoices')->group(function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::get('/create', [InvoiceController::class, 'create'])->middleware('can:create invoices')->name('create');
        Route::post('/', [InvoiceController::class, 'store'])->middleware('can:create invoices')->name('store');
        Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show');
        Route::get('/{invoice}/edit', [InvoiceController::class, 'edit'])->middleware('can:edit invoices')->name('edit');
        Route::put('/{invoice}', [InvoiceController::class, 'update'])->middleware('can:edit invoices')->name('update');
        Route::delete('/{invoice}', [InvoiceController::class, 'destroy'])->middleware('can:delete invoices')->name('destroy');
        Route::post('/{invoice}/send', [InvoiceController::class, 'send'])->name('send');
        Route::post('/{invoice}/rollback', [InvoiceController::class, 'rollback'])->name('rollback');
        Route::post('/{invoice}/payments', [PaymentController::class, 'store'])->middleware('can:create invoices')->name('payments.store');
    });

    Route::prefix('payments')->name('payments.')->middleware('can:edit invoices')->group(function () {
        Route::post('/{payment}', [PaymentController::class, 'update'])->name('update');
        Route::delete('/{payment}', [PaymentController::class, 'destroy'])->name('destroy');
    });

    // Invoice PDF Operations
    Route::prefix('invoice')->name('invoice.')->group(function () {
        Route::get('/{invoice}/download', function (Invoice $invoice, Request $request) {
            $service = new InvoicePrintService;
            $dpAmount = $request->query('dp_amount') ? (int) $request->query('dp_amount') : null;
            $pelunasanAmount = $request->query('pelunasan_amount') ? (int) $request->query('pelunasan_amount') : null;
            $template = $request->query('template', 'kisantra-invoice'); // Template parameter
            $pdf = $service->generateSingleInvoicePdf($invoice, $dpAmount, $pelunasanAmount, $template);

            $invoiceType = $dpAmount ? 'DP-' : ($pelunasanAmount ? 'Pelunasan-' : '');
            $filename = $invoiceType.'Invoice-'.str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $invoice->invoice_number).'.pdf';

            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, $filename, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            ]);
        })->middleware('can:view invoices')->name('download');

        Route::get('/{invoice}/preview', function (Invoice $invoice, Request $request) {
            $service = new InvoicePrintService;
            $dpAmount = $request->query('dp_amount') ? (int) $request->query('dp_amount') : null;
            $pelunasanAmount = $request->query('pelunasan_amount') ? (int) $request->query('pelunasan_amount') : null;
            $template = $request->query('template', 'kisantra-invoice'); // Template parameter
            $pdf = $service->generateSingleInvoicePdf($invoice, $dpAmount, $pelunasanAmount, $template);

            return response($pdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline',
            ]);
        })->middleware('can:view invoices')->name('preview');
    });

    // ------------------------------------------------------------------------
    // FINANCE - RECURRING INVOICES
    // ------------------------------------------------------------------------
    Route::prefix('recurring-invoices')->name('recurring-invoices.')->group(function () {
        Route::get('/', [RecurringInvoiceController::class, 'index'])
            ->middleware('can:view recurring-invoices')
            ->name('index');

        // Templates
        Route::get('/templates/create', [RecurringInvoiceController::class, 'createTemplate'])
            ->middleware('can:create recurring-invoices')
            ->name('templates.create');
        Route::get('/templates/{template}/edit', [RecurringInvoiceController::class, 'editTemplate'])
            ->middleware('can:edit recurring-invoices')
            ->name('templates.edit');
        Route::post('/templates', [RecurringInvoiceController::class, 'storeTemplate'])
            ->middleware('can:create recurring-invoices')
            ->name('templates.store');
        Route::put('/templates/{template}', [RecurringInvoiceController::class, 'updateTemplate'])
            ->middleware('can:edit recurring-invoices')
            ->name('templates.update');
        Route::delete('/templates/{template}', [RecurringInvoiceController::class, 'destroyTemplate'])
            ->middleware('can:edit recurring-invoices')
            ->name('templates.destroy');
        Route::post('/templates/{template}/restore', [RecurringInvoiceController::class, 'restoreTemplate'])
            ->middleware('can:edit recurring-invoices')
            ->name('templates.restore');

        // Monthly invoices
        Route::post('/monthly/generate', [RecurringInvoiceController::class, 'generateMonthly'])
            ->middleware('can:create recurring-invoices')
            ->name('monthly.generate');
        Route::post('/monthly', [RecurringInvoiceController::class, 'storeMonthly'])
            ->middleware('can:create recurring-invoices')
            ->name('monthly.store');
        Route::put('/monthly/{invoice}', [RecurringInvoiceController::class, 'updateMonthly'])
            ->middleware('can:edit recurring-invoices')
            ->name('monthly.update');
        Route::delete('/monthly/{invoice}', [RecurringInvoiceController::class, 'destroyMonthly'])
            ->middleware('can:edit recurring-invoices')
            ->name('monthly.destroy');
        Route::post('/monthly/{invoice}/publish', [RecurringInvoiceController::class, 'publishMonthly'])
            ->middleware('can:edit recurring-invoices')
            ->name('monthly.publish');
        Route::post('/monthly/bulk-destroy', [RecurringInvoiceController::class, 'bulkDestroyMonthly'])
            ->middleware('can:edit recurring-invoices')
            ->name('monthly.bulk-destroy');
        Route::post('/monthly/bulk-publish', [RecurringInvoiceController::class, 'bulkPublishMonthly'])
            ->middleware('can:edit recurring-invoices')
            ->name('monthly.bulk-publish');
    });

    // ------------------------------------------------------------------------
    // FINANCE - BANK & CASH FLOW
    // ------------------------------------------------------------------------
    Route::middleware('can:view bank-accounts')->group(function () {
        Route::get('/bank-accounts', [BankAccountController::class, 'index'])->name('bank-accounts.index');
        Route::post('/bank-accounts', [BankAccountController::class, 'store'])
            ->middleware('can:create bank-accounts')
            ->name('bank-accounts.store');
        Route::put('/bank-accounts/{bankAccount}', [BankAccountController::class, 'update'])
            ->middleware('can:edit bank-accounts')
            ->name('bank-accounts.update');
        Route::delete('/bank-accounts/{bankAccount}', [BankAccountController::class, 'destroy'])
            ->middleware('can:delete bank-accounts')
            ->name('bank-accounts.destroy');

        // Transactions for the selected account (JSON endpoints)
        Route::get('/bank-accounts/transactions', [BankTransactionController::class, 'indexTransactions'])
            ->name('bank-accounts.transactions');
        Route::get('/bank-accounts/payments', [BankTransactionController::class, 'indexPayments'])
            ->name('bank-accounts.payments');

        Route::post('/bank-transactions', [BankTransactionController::class, 'store'])
            ->middleware('can:create transactions')
            ->name('bank-transactions.store');
        Route::put('/bank-transactions/{bankTransaction}', [BankTransactionController::class, 'update'])
            ->middleware('can:edit transactions')
            ->name('bank-transactions.update');
        Route::delete('/bank-transactions/{bankTransaction}', [BankTransactionController::class, 'destroy'])
            ->middleware('can:delete transactions')
            ->name('bank-transactions.destroy');
        Route::post('/bank-transactions/bulk-delete', [BankTransactionController::class, 'bulkDestroy'])
            ->middleware('can:delete transactions')
            ->name('bank-transactions.bulk-destroy');
        Route::post('/bank-transactions/transfer', [BankTransactionController::class, 'transfer'])
            ->middleware('can:create transactions')
            ->name('bank-transactions.transfer');
    });

    Route::get('/bank-account/export/pdf', [CashFlowExportController::class, 'exportPdf'])
        ->middleware('can:view bank-accounts')
        ->name('bank-account.export.pdf');

    Route::get('/bank-account/export/pdf/preview', [CashFlowExportController::class, 'previewPdf'])
        ->middleware('can:view bank-accounts')
        ->name('bank-account.export.pdf.preview');

    Route::prefix('cash-flow')->name('cash-flow.')->middleware('can:view cash-flow')->group(function () {
        Route::get('/', fn () => redirect()->route('cash-flow.income'))->name('index');
        Route::get('/income', [CashFlowController::class, 'income'])->name('income');
        Route::get('/expenses', [CashFlowController::class, 'expenses'])->name('expenses');
        Route::get('/transfers', [CashFlowController::class, 'transfers'])->name('transfers');
        Route::post('/bulk-delete', [CashFlowController::class, 'bulkDestroy'])
            ->middleware('can:delete transactions')
            ->name('bulk-destroy');

        Route::get('/export/pdf', [CashFlowExportController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/export/pdf/preview', [CashFlowExportController::class, 'previewPdf'])->name('export.pdf.preview');
    });

    // ------------------------------------------------------------------------
    // CATEGORIES
    // ------------------------------------------------------------------------
    Route::middleware('can:view categories')->group(function () {
        Route::get('/transaction-categories', [TransactionCategoryController::class, 'index'])->name('transaction-categories.index');
        Route::post('/transaction-categories', [TransactionCategoryController::class, 'store'])->middleware('can:create categories')->name('transaction-categories.store');
        Route::put('/transaction-categories/{transactionCategory}', [TransactionCategoryController::class, 'update'])->middleware('can:edit categories')->name('transaction-categories.update');
        Route::delete('/transaction-categories/{transactionCategory}', [TransactionCategoryController::class, 'destroy'])->middleware('can:delete categories')->name('transaction-categories.destroy');
    });

    // ------------------------------------------------------------------------
    // REIMBURSEMENTS
    // ------------------------------------------------------------------------
    Route::middleware('can:view reimbursements')->group(function () {
        Route::get('/reimbursements', [ReimbursementController::class, 'index'])->name('reimbursements.index');
        Route::get('/reimbursements/create', [ReimbursementController::class, 'create'])->middleware('can:create reimbursements')->name('reimbursements.create');
        Route::post('/reimbursements', [ReimbursementController::class, 'store'])->middleware('can:create reimbursements')->name('reimbursements.store');
        Route::get('/reimbursements/{reimbursement}/edit', [ReimbursementController::class, 'edit'])->middleware('can:edit reimbursements')->name('reimbursements.edit');
        Route::put('/reimbursements/{reimbursement}', [ReimbursementController::class, 'update'])->middleware('can:edit reimbursements')->name('reimbursements.update');
        Route::delete('/reimbursements/{reimbursement}', [ReimbursementController::class, 'destroy'])->middleware('can:delete reimbursements')->name('reimbursements.destroy');
        Route::post('/reimbursements/{reimbursement}/submit', [ReimbursementController::class, 'submit'])->name('reimbursements.submit');
        Route::post('/reimbursements/{reimbursement}/review', [ReimbursementController::class, 'review'])->middleware('can:approve reimbursements')->name('reimbursements.review');
        Route::post('/reimbursements/{reimbursement}/pay', [ReimbursementController::class, 'pay'])->middleware('can:pay reimbursements')->name('reimbursements.pay');
    });

    // ------------------------------------------------------------------------
    // FUND REQUESTS
    // ------------------------------------------------------------------------
    Route::middleware('can:view fund requests')->group(function () {
        Route::get('/fund-requests', [FundRequestController::class, 'index'])->name('fund-requests.index');
        Route::get('/fund-requests/create', [FundRequestController::class, 'create'])->middleware('can:create fund requests')->name('fund-requests.create');
        Route::post('/fund-requests', [FundRequestController::class, 'store'])->middleware('can:create fund requests')->name('fund-requests.store');
        Route::get('/fund-requests/{fundRequest}/edit', [FundRequestController::class, 'edit'])->middleware('can:edit fund requests')->name('fund-requests.edit');
        Route::put('/fund-requests/{fundRequest}', [FundRequestController::class, 'update'])->middleware('can:edit fund requests')->name('fund-requests.update');
        Route::delete('/fund-requests/{fundRequest}', [FundRequestController::class, 'destroy'])->middleware('can:delete fund requests')->name('fund-requests.destroy');
        Route::post('/fund-requests/{fundRequest}/submit', [FundRequestController::class, 'submit'])->name('fund-requests.submit');
        Route::post('/fund-requests/{fundRequest}/review', [FundRequestController::class, 'review'])->middleware('can:approve fund requests')->name('fund-requests.review');
        Route::post('/fund-requests/{fundRequest}/disburse', [FundRequestController::class, 'disburse'])->middleware('can:disburse fund requests')->name('fund-requests.disburse');
    });

    Route::get('/fund-requests/export/pdf', function (Request $request) {
        $filters = [
            'month' => $request->query('month'),
            'status' => $request->query('status'),
            'priority' => $request->query('priority'),
            'user_id' => $request->query('user_id') ? (int) $request->query('user_id') : null,
            'search' => $request->query('search'),
        ];
        $showRequestor = (bool) $request->query('show_requestor', false);

        $service = new FundRequestExportService;
        $pdf = $service->generate($filters, $showRequestor);

        $month = $filters['month'] ?? 'all';
        $filename = 'Rekap-Pengajuan-Dana-'.$month.'.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    })->middleware('can:view fund requests')->name('fund-requests.export.pdf');

    Route::get('/fund-requests/export/pdf/preview', function (Request $request) {
        $filters = [
            'month' => $request->query('month'),
            'status' => $request->query('status'),
            'priority' => $request->query('priority'),
            'user_id' => $request->query('user_id') ? (int) $request->query('user_id') : null,
            'search' => $request->query('search'),
        ];
        $showRequestor = (bool) $request->query('show_requestor', false);

        $service = new FundRequestExportService;
        $pdf = $service->generate($filters, $showRequestor);

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline',
        ]);
    })->middleware('can:view fund requests')->name('fund-requests.export.pdf.preview');

    // ------------------------------------------------------------------------
    // FEEDBACKS
    // ------------------------------------------------------------------------
    Route::get('/feedbacks', FeedbacksIndex::class)
        ->middleware('can:view feedbacks')
        ->name('feedbacks.index');

    // ------------------------------------------------------------------------
    // DEBT & RECEIVABLES
    // ------------------------------------------------------------------------
    Route::middleware('can:view loans')->group(function () {
        Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');
        Route::post('/loans', [LoanController::class, 'store'])->middleware('can:create loans')->name('loans.store');
        Route::put('/loans/{loan}', [LoanController::class, 'update'])->middleware('can:edit loans')->name('loans.update');
        Route::delete('/loans/{loan}', [LoanController::class, 'destroy'])->middleware('can:delete loans')->name('loans.destroy');
        Route::post('/loans/{loan}/pay', [LoanController::class, 'pay'])->middleware('can:pay loans')->name('loans.pay');
    });

    Route::middleware('can:view receivables')->group(function () {
        Route::get('/receivables', [ReceivableController::class, 'index'])->name('receivables.index');
        Route::post('/receivables', [ReceivableController::class, 'store'])->middleware('can:create receivables')->name('receivables.store');
        Route::put('/receivables/{receivable}', [ReceivableController::class, 'update'])->middleware('can:edit receivables')->name('receivables.update');
        Route::delete('/receivables/{receivable}', [ReceivableController::class, 'destroy'])->middleware('can:delete receivables')->name('receivables.destroy');
        Route::post('/receivables/{receivable}/submit', [ReceivableController::class, 'submit'])->name('receivables.submit');
        Route::post('/receivables/{receivable}/approve', [ReceivableController::class, 'approve'])->middleware('can:approve receivables')->name('receivables.approve');
        Route::post('/receivables/{receivable}/pay', [ReceivableController::class, 'pay'])->middleware('can:pay receivables')->name('receivables.pay');
    });

    // ------------------------------------------------------------------------
    // ADMINISTRATION - PERMISSIONS
    // ------------------------------------------------------------------------
    Route::get('/permissions', PermissionsIndex::class)
        ->middleware('can:view permissions')
        ->name('permissions.index');

    // ------------------------------------------------------------------------
    // ADMINISTRATION - USERS
    // ------------------------------------------------------------------------
    Route::get('/admin/users', UsersIndex::class)
        ->middleware('can:manage users')
        ->name('admin.users');

    // ------------------------------------------------------------------------
    // SETTINGS
    // ------------------------------------------------------------------------
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::redirect('/', '/settings/profile');
        Route::get('/profile', Profile::class)->name('profile');
        Route::get('/password', Password::class)->name('password');
        Route::get('/company', CompanyProfileSettings::class)->name('company');
    });

    // ------------------------------------------------------------------------
    // API ENDPOINTS
    // ------------------------------------------------------------------------

    // ------------------------------------------------------------------------
    // TESTING (Local Only)
    // ------------------------------------------------------------------------
    Route::get('/test', TestingPage::class)->name('test');

});

// Language switching (used by React AppLayout)
Route::post('/language', function (Request $request) {
    $locale = $request->input('locale');
    $available = config('app.available_locales', ['id', 'en', 'zh']);
    if (in_array($locale, $available)) {
        session(['locale' => $locale]);
        if (auth()->check()) {
            auth()->user()->update(['locale' => $locale]);
        }
    }

    return redirect()->back();
})->middleware('web')->name('language.switch');

require __DIR__.'/auth.php';
