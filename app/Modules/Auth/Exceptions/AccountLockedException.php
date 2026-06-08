<?php

namespace App\Modules\Auth\Exceptions;

use Carbon\Carbon;
use Exception;

class AccountLockedException extends Exception
{
    public function __construct(Carbon $lockedUntil)
    {
        parent::__construct(
            "Account locked until {$lockedUntil->toTimeString()}. Try again later."
        );
    }
}
