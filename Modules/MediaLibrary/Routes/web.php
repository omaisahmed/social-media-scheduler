<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\MediaLibrary\Http\Controllers\MediaLibraryController;

Route::middleware(['auth', 'verified', 'business.setup'])->prefix('media')->name('media.')->group(function () {
    Route::get('/', [MediaLibraryController::class, 'index'])->name('index');
    Route::post('/', [MediaLibraryController::class, 'store'])->name('store');
    Route::post('bulk-delete', [MediaLibraryController::class, 'bulkDestroy'])->name('bulk-destroy');
    Route::delete('{asset}', [MediaLibraryController::class, 'destroy'])->name('destroy');
});
