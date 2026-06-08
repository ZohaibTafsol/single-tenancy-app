<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Auth\Http\Controllers\AuthController;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])
        // ->middleware('throttle:api-login')
        ->name('auth.login');

    // ── Requires any valid Sanctum token (temp or full) ──────────────
    Route::middleware('auth:sanctum')->group(function () {

        Route::post('/mfa/verify',  [AuthController::class, 'verifyMfa'])->name('auth.mfa.verify');
        Route::post('/refresh',     [AuthController::class, 'refresh'])->name('auth.refresh');
        Route::post('/logout',      [AuthController::class, 'logout'])->name('auth.logout');
        Route::post('/logout-all',  [AuthController::class, 'logoutAll'])->name('auth.logout.all');

        // MFA management (needs credentials but not necessarily MFA confirmed)
        Route::prefix('mfa')->name('auth.mfa.')->group(function () {
            Route::post('/setup',   [AuthController::class, 'setupMfa'])->name('setup');
            Route::post('/confirm', [AuthController::class, 'confirmMfa'])->name('confirm');
            Route::post('/disable', [AuthController::class, 'disableMfa'])->name('disable');
        });
    });
});
