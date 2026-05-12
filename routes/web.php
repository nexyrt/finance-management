<?php

use App\Http\Controllers\CashFlowExportController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TransactionCategoryController;
use App\Livewire\Accounts\Index as AccountsIndex;
use App\Livewire\CashFlow\ExpensesPage as CashFlowExpenses;
use App\Livewire\CashFlow\Income as CashFlowIncome;
use App\Livewire\CashFlow\Transfers as CashFlowTransfers;
use App\Livewire\Dashboard;
use App\Livewire\Feedbacks\Index as FeedbacksIndex;
use App\Livewire\FundRequests\Index as FundRequestsIndex;
use App\Livewire\Loans\Index as LoansIndex;
use App\Livewire\Permissions\Index as PermissionsIndex;
use App\Livewire\Receivables\Index as ReceivablesIndex;
use App\Livewire\RecurringInvoices\CreateTemplate as RecurringInvoicesCreateTemplate;
use App\Livewire\RecurringInvoices\EditTemplate as RecurringInvoicesEditTemplate;
use App\Livewire\RecurringInvoices\Index as RecurringInvoicesIndex;
use App\Livewire\RecurringInvoices\Monthly\CreateInvoice as RecurringInvoicesMonthlyCreate;
use App\Livewire\RecurringInvoices\Monthly\EditInvoice as RecurringInvoicesMonthlyEdit;
use App\Livewire\Reimbursements\Index as ReimbursementIndex;
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
        Route::get('/', RecurringInvoicesIndex::class)
            ->middleware('can:view recurring-invoices')
            ->name('index');

        Route::get('/template/create', RecurringInvoicesCreateTemplate::class)
            ->middleware('can:create recurring-invoices')
            ->name('template.create');

        Route::get('/template/{template}/edit', RecurringInvoicesEditTemplate::class)
            ->middleware('can:edit recurring-invoices')
            ->name('template.edit');

        Route::get('/monthly/create', RecurringInvoicesMonthlyCreate::class)
            ->middleware('can:create recurring-invoices')
            ->name('monthly.create');

        Route::get('/monthly/{invoice}/edit', RecurringInvoicesMonthlyEdit::class)
            ->middleware('can:edit recurring-invoices')
            ->name('monthly.edit');
    });

    // ------------------------------------------------------------------------
    // FINANCE - BANK & CASH FLOW
    // ------------------------------------------------------------------------
    Route::get('/bank-accounts', AccountsIndex::class)
        ->middleware('can:view bank-accounts')
        ->name('bank-accounts.index');

    Route::get('/bank-account/export/pdf', [CashFlowExportController::class, 'exportPdf'])
        ->middleware('can:view bank-accounts')
        ->name('bank-account.export.pdf');

    Route::get('/bank-account/export/pdf/preview', [CashFlowExportController::class, 'previewPdf'])
        ->middleware('can:view bank-accounts')
        ->name('bank-account.export.pdf.preview');

    Route::prefix('cash-flow')->name('cash-flow.')->middleware('can:view cash-flow')->group(function () {
        Route::get('/', fn () => redirect()->route('cash-flow.income'))->name('index');
        Route::get('/income', CashFlowIncome::class)->name('income');
        Route::get('/expenses', CashFlowExpenses::class)->name('expenses');
        Route::get('/transfers', CashFlowTransfers::class)->name('transfers');

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
    Route::get('/reimbursements', ReimbursementIndex::class)
        ->middleware('can:view reimbursements')
        ->name('reimbursements.index');

    // ------------------------------------------------------------------------
    // FUND REQUESTS
    // ------------------------------------------------------------------------
    Route::get('/fund-requests', FundRequestsIndex::class)
        ->middleware('can:view fund requests')
        ->name('fund-requests.index');

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
    Route::get('/loans', LoansIndex::class)
        ->middleware('can:view loans')
        ->name('loans.index');

    Route::get('/receivables', ReceivablesIndex::class)
        ->middleware('can:view receivables')
        ->name('receivables.index');

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
