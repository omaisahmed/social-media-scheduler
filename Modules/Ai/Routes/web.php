<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\AI\Http\Controllers\AiController;

Route::middleware(['auth', 'verified', 'business.setup'])->prefix('ai')->name('ai.')->group(function () {
    Route::get('/', [AiController::class, 'index'])->name('index');
    Route::post('generate', [AiController::class, 'generate'])->name('generate');
});
