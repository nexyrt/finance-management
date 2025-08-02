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
use Illuminate\Support\Facades\Route;

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

    });

    Route::get('/temp-pdf/{filename}', function ($filename) {
        $path = storage_path('app/private/temp/' . $filename);
        if (!file_exists($path))
            abort(404);

        return response()->file($path, [
            'Content-Type' => 'application/pdf'
        ]);
    })->name('temp.pdf');

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