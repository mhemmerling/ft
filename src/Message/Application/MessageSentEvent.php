<?php

declare(strict_types=1);

namespace App\Message\Application;

use App\Message\Domain\Message;

final class MessageSentEvent
{
    public function __construct(
        private readonly Message $message
    ) {
    }

    public function getMessage(): Message
    {
        return $this->message;
    }
}
