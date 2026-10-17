<?php

use App\Livewire\Asset\Dividends;
use App\Livewire\Asset\Index;
use App\Livewire\Asset\Show;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Transaction\Index as TransactionIndex;
use App\Livewire\Wallet\Index as WalletIndex;
use App\Livewire\Wallet\Show as WalletShow;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');
    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
    Route::get('/my-transactions', TransactionIndex::class)->name('my-transactions');
    Route::get('/my-wallet/{wallet}/transactions', TransactionIndex::class)->name('my-wallet.transactions');
    Route::get('/assets', Index::class)->name('assets');
    Route::get('/assets/{asset}', Show::class)->name('assets.show');
    Route::get('/assets/{asset}/dividend', Dividends::class)->name('assets.dividends');
    Route::get('/assets/{asset}/transactions', TransactionIndex::class)->name('assets.transactions');
    Route::get('/my-wallet/{wallet}', WalletShow::class)->name('my-wallet.show');
    Route::get('/my-wallets', WalletIndex::class)->name('my-wallets');

});

require __DIR__.'/auth.php';
