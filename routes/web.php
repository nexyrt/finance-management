<?php

use App\Livewire\BankAccounts;
use App\Livewire\ClientManagement;
use App\Livewire\Clients\Index as ClientIndex;
use App\Livewire\Dashboard;
use App\Livewire\ServiceManagement;
use App\Livewire\InvoiceManagement;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\TestingPage;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login')->name('home');

// Group all routes requiring auth and verification
Route::middleware(['auth', 'verified'])->group(function () {
    // Master Data
    Route::get('dashboard', Dashboard::class)->name('dashboard');
    Route::get('/clients', ClientIndex::class)->name('clients');
    Route::get('/services', ServiceManagement::class)->name('services');
    Route::get('/bank-accounts', BankAccounts::class)->name('bank-accounts');

    // Features
    Route::get('/invoices', InvoiceManagement::class)->name('invoices');

    Route::get('/bank-accounts', BankAccounts::class)->name('bank-accounts');
    Route::get('/test', TestingPage::class)->name('test');
});

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
}); 

require __DIR__.'/auth.php';
