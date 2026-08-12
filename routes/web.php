<?php

use Illuminate\Support\Facades\Route;
use Vendor\MetaLeadIngester\Http\Controllers\Web\DashboardController;

Route::get('/', [DashboardController::class, 'index'])->name('meta-lead-ingester.dashboard');

// Meta Accounts
Route::post('/meta-accounts', [DashboardController::class, 'storeMetaAccount'])->name('meta-lead-ingester.meta-accounts.store');
Route::delete('/meta-accounts/{id}', [DashboardController::class, 'destroyMetaAccount'])->name('meta-lead-ingester.meta-accounts.destroy');

// Google Accounts
Route::post('/google-accounts', [DashboardController::class, 'storeGoogleAccount'])->name('meta-lead-ingester.google-accounts.store');
Route::delete('/google-accounts/{id}', [DashboardController::class, 'destroyGoogleAccount'])->name('meta-lead-ingester.google-accounts.destroy');
