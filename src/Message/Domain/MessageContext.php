<?php

declare(strict_types=1);

namespace App\Message\Domain;

use App\Shared\Exception\InvalidMessageContextException;

enum MessageContext: string
{
    case USER = 'USER';
    case SYSTEM = 'SYSTEM';
    case AD = 'AD';

    public static function getContext(string $context): string
    {
        return match ($context) {
            MessageContext::USER->value => MessageContext::USER->value,
            MessageContext::SYSTEM->value => MessageContext::SYSTEM->value,
            MessageContext::AD->value => MessageContext::AD->value,
            default => throw new InvalidMessageContextException('Incorrect context:'.$context),
        };
    }
}
