<?php

namespace App\Modules\Payer\Exceptions;

use Exception;

class PayerNotFoundException extends Exception
{
    public function __construct(?int $id = null)
    {
        $message = $id
            ? "Payer with ID {$id} not found."
            : "Payer not found.";

        parent::__construct($message);
    }
}