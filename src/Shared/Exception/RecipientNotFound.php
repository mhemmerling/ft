<?php

declare(strict_types=1);

namespace App\Shared\Exception;

class RecipientNotFound extends \Exception
{
    public function __construct(int $recipientId)
    {
        parent::__construct(
            'Recipient with id '.$recipientId.' not found',
            ExceptionCode::RecipientNotFound->value
        );
    }
}
