<?php

namespace App\Modules\Auth\Exceptions;

use Exception;

class InvalidMfaCodeException extends Exception
{
    public function __construct()
    {
        parent::__construct('Invalid or expired OTP code.');
    }
}
