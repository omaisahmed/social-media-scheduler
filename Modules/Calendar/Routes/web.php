<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Calendar\Http\Controllers\CalendarController;

Route::middleware(['auth', 'verified', 'business.setup'])->prefix('calendar')->name('calendar.')->group(function () {
    Route::get('/', [CalendarController::class, 'index'])->name('index');
});
