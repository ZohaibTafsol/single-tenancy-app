<?php

namespace App\Modules\Auth\Exceptions;

use Exception;

class InvalidTokenScopeException extends Exception
{
    public function __construct(string $message = 'Token does not have the required scope.')
    {
        parent::__construct($message);
    }
}
