<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Payer\Http\Controllers\PayerController;


Route::resource('payers', PayerController::class);
