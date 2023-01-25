<?php

declare(strict_types=1);

namespace App\Message\Command;

class ChangeMessageStatus
{
    public function __construct(
        private readonly int $userId,
        private readonly int $messageId,
        private readonly bool $read
    ) {
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getMessageId(): int
    {
        return $this->messageId;
    }

    public function isRead(): bool
    {
        return $this->read;
    }
}
