<?php

declare(strict_types=1);

namespace App\Shared\Exception;

enum ExceptionCode: int
{
    case NoRecipients = 1;
    case NoMessage = 2;
    case NoMessageId = 3;
    case RecipientNotFound = 4;
    case InvalidMessageContext = 5;
}
