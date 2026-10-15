<?php

namespace App\Exceptions;

use RuntimeException;

class SlotUnavailableException extends RuntimeException
{
    public function __construct(string $message = 'Jam ini sudah terisi, coba jam lain.')
    {
        parent::__construct($message);
    }
}
