<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Notifications\Http\Controllers\NotificationController;

Route::middleware(['auth', 'verified', 'business.setup'])->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::post('read-all', [NotificationController::class, 'readAll'])->name('read-all');
    Route::post('{id}/read', [NotificationController::class, 'read'])->name('read');
});
