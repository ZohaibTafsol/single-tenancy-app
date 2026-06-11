<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Recipient\Http\Controllers\RecipientController;


Route::resource('recipients', RecipientController::class);
Route::patch('recipients/{uuid}/status', [RecipientController::class, 'status']);
