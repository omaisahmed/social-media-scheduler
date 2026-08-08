<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Business\Http\Controllers\BusinessController;
use Modules\Business\Http\Controllers\OnboardingController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('onboarding', [OnboardingController::class, 'show'])->name('onboarding.business');
    Route::post('onboarding', [OnboardingController::class, 'store'])->name('onboarding.business.store');
    Route::get('onboarding/next', [OnboardingController::class, 'next'])->name('onboarding.next');

    Route::get('business', [BusinessController::class, 'index'])->name('business.index');
    Route::get('business/create', [BusinessController::class, 'create'])->name('business.create');
    Route::post('business', [BusinessController::class, 'store'])->name('business.store');
    Route::get('business/{business}/edit', [BusinessController::class, 'edit'])->name('business.edit');
    Route::patch('business/{business}', [BusinessController::class, 'update'])->name('business.update');
    Route::delete('business/{business}', [BusinessController::class, 'destroy'])->name('business.destroy');
});
