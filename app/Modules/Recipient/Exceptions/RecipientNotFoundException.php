<?php

namespace App\Modules\Recipient\Exceptions;

use Exception;

class RecipientNotFoundException extends Exception
{
    public function __construct(?int $id = null)
    {
        $message = $id
            ? "Recipient with ID {$id} not found."
            : "Recipient not found.";

        parent::__construct($message);
    }
}