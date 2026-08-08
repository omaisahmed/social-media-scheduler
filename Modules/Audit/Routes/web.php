<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Audit\Http\Controllers\AuditController;

Route::middleware(['auth', 'verified', 'business.setup'])->prefix('audit')->name('audit.')->group(function () {
    Route::get('/', [AuditController::class, 'index'])->name('index');
});
