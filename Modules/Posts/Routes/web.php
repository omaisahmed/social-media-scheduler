<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Posts\Http\Controllers\PostController;

Route::middleware(['auth', 'verified', 'business.setup'])->prefix('posts')->name('posts.')->group(function () {
    Route::get('/', [PostController::class, 'index'])->name('index');
    Route::get('create', [PostController::class, 'create'])->name('create');
    Route::post('/', [PostController::class, 'store'])->name('store');
    Route::get('{post}', [PostController::class, 'show'])->name('show');
    Route::get('{post}/edit', [PostController::class, 'edit'])->name('edit');
    Route::patch('{post}', [PostController::class, 'update'])->name('update');
    Route::post('{post}/publish', [PostController::class, 'publish'])->name('publish');
    Route::post('{post}/cancel', [PostController::class, 'cancel'])->name('cancel');
    Route::delete('{post}', [PostController::class, 'destroy'])->name('destroy');
});
