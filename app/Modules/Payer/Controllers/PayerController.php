<?php

declare(strict_types=1);

namespace App\Modules\Payer\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PayerController extends Controller
{
    public function index()
    {
        return view('payer.index');
    }
}
