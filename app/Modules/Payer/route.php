<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Payer\Http\Controllers\PayerController;

Route::prefix('v1')->group(function () {
    Route::resource('payers', PayerController::class);
});
