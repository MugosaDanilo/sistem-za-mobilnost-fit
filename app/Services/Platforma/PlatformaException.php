<?php

namespace App\Services\Platforma;

use RuntimeException;

class PlatformaException extends RuntimeException
{
    public function __construct(string $message, public readonly int $status = 0, public readonly array $body = [])
    {
        parent::__construct($message);
    }
}
