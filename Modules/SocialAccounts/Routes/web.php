<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\SocialAccounts\Http\Controllers\SocialAccountController;

Route::middleware(['auth', 'verified', 'business.setup'])->prefix('social-accounts')->name('social-accounts.')->group(function () {
    Route::get('/', [SocialAccountController::class, 'index'])->name('index');
    Route::post('/', [SocialAccountController::class, 'connect'])->name('connect');
    Route::delete('{account}', [SocialAccountController::class, 'disconnect'])->name('disconnect');
});
