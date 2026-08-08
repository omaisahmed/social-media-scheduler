<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Settings\Http\Controllers\SettingsController;

Route::middleware(['auth', 'verified', 'business.setup'])->prefix('settings')->name('settings.')->group(function () {
    Route::get('/', [SettingsController::class, 'index'])->name('index');
    Route::post('business', [SettingsController::class, 'updateBusiness'])->name('business');
    Route::post('notifications', [SettingsController::class, 'updateNotifications'])->name('notifications');
});
