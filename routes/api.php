<?php

use Illuminate\Support\Facades\Route;
use Vendor\MetaLeadIngester\Http\Controllers\WebhookController;
use Vendor\MetaLeadIngester\Http\Middleware\VerifyMetaSignature;

Route::get('/webhook', [WebhookController::class, 'verify'])->name('meta-lead-ingester.verify');
Route::post('/webhook', [WebhookController::class, 'receive'])
    ->middleware(VerifyMetaSignature::class)
    ->name('meta-lead-ingester.receive');
