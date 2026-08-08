<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Teams\Http\Controllers\InvitationController;
use Modules\Teams\Http\Controllers\TeamController;

Route::middleware(['auth', 'verified', 'business.setup'])->prefix('team')->name('teams.')->group(function () {
    Route::get('/', [TeamController::class, 'index'])->name('index');
    Route::patch('members/{user}/role', [TeamController::class, 'updateRole'])->name('role');
    Route::delete('members/{user}', [TeamController::class, 'remove'])->name('remove');

    Route::post('invitations', [InvitationController::class, 'store'])->name('invite');
    Route::delete('invitations/{invitation}', [InvitationController::class, 'revoke'])->name('invite.revoke');
});
