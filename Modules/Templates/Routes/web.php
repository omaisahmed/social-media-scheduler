<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Templates\Http\Controllers\TemplateController;

Route::middleware(['auth', 'verified', 'business.setup'])->prefix('templates')->name('templates.')->group(function () {
    Route::get('/', [TemplateController::class, 'index'])->name('index');
    Route::get('create', [TemplateController::class, 'create'])->name('create');
    Route::post('/', [TemplateController::class, 'store'])->name('store');
    Route::get('{template}/edit', [TemplateController::class, 'edit'])->name('edit');
    Route::patch('{template}', [TemplateController::class, 'update'])->name('update');
    Route::delete('{template}', [TemplateController::class, 'destroy'])->name('destroy');
});
