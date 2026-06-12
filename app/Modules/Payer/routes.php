<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Payer\Http\Controllers\PayerController;


Route::resource('payers', PayerController::class);
Route::patch('payers/{uuid}/status', [PayerController::class, 'status']);

    Route::get('payers/bulk-upload/template', [PayerController::class, 'downloadTemplate']);
    Route::post('payers/bulk-upload',         [PayerController::class, 'bulkUpload']);