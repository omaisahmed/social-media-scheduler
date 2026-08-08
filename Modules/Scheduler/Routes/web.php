<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Scheduler\Http\Controllers\SchedulerController;

Route::middleware(['auth', 'verified', 'business.setup'])->prefix('scheduler')->name('scheduler.')->group(function () {
    Route::get('/', [SchedulerController::class, 'index'])->name('index');
    Route::post('/', [SchedulerController::class, 'store'])->name('store');
    Route::delete('{window}', [SchedulerController::class, 'destroy'])->name('destroy');
});
