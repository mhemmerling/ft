<?php

declare(strict_types=1);

namespace App\Shared\Exception;

class InvalidInput extends \Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message, ExceptionCode::InvalidMessageContext->value);
    }
}
