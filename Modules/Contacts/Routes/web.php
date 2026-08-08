<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Contacts\Http\Controllers\ContactController;

Route::middleware(['auth', 'verified', 'business.setup'])->prefix('contacts')->name('contacts.')->group(function () {
    Route::get('search', [ContactController::class, 'search'])->name('search');
    Route::post('import', [ContactController::class, 'import'])->name('import');
    Route::post('from-remote', [ContactController::class, 'storeFromRemote'])->name('storeRemote');
    Route::get('/', [ContactController::class, 'index'])->name('index');
    Route::get('create', [ContactController::class, 'create'])->name('create');
    Route::post('/', [ContactController::class, 'store'])->name('store');
    Route::get('{contact}/edit', [ContactController::class, 'edit'])->name('edit');
    Route::patch('{contact}', [ContactController::class, 'update'])->name('update');
    Route::delete('{contact}', [ContactController::class, 'destroy'])->name('destroy');
});
