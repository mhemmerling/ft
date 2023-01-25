<?php

declare(strict_types=1);

namespace App\Shared\Exception;

class NoRecipientsException extends \Exception
{
    public function __construct()
    {
        parent::__construct('No recipients', ExceptionCode::NoRecipients->value);
    }
}
