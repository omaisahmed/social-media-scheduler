<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Reports\Http\Controllers\ReportsController;

Route::middleware(['auth', 'verified', 'business.setup'])->prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [ReportsController::class, 'index'])->name('index');
    Route::post('generate', [ReportsController::class, 'generate'])->name('generate');
    Route::get('{export}/download', [ReportsController::class, 'download'])->name('download');
    Route::delete('{export}', [ReportsController::class, 'destroy'])->name('destroy');
});
