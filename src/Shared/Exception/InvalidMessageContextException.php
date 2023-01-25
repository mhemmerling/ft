<?php

declare(strict_types=1);

namespace App\Shared\Exception;

class InvalidMessageContextException extends \Exception
{
    public function __construct(string $context)
    {
        parent::__construct('Incorrect context:'.$context, ExceptionCode::InvalidMessageContext->value);
    }
}
