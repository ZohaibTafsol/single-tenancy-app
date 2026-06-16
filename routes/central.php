<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->group(function () {
        require app_path('Modules/Auth/routes.php');

        Route::middleware('auth:sanctum')->group(function () {
            Route::apiResource('tenants', \App\Modules\Tenant\Http\Controllers\TenantController::class);
            require app_path('Modules/Payer/routes.php');
            require app_path('Modules/Recipient/routes.php');
            // require app_path('Modules/Tenant/routes.php');
        });
    });
