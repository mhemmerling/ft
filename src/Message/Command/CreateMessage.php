<?php

declare(strict_types=1);

namespace App\Message\Command;

use Symfony\Component\Security\Core\User\UserInterface;

final class CreateMessage
{
    public function __construct(
        private readonly string $title,
        private readonly string $content,
        private readonly UserInterface $sender,
        private readonly string $context,
        private readonly array $recipients
    ) {
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getSender(): UserInterface
    {
        return $this->sender;
    }

    public function getRecipients(): array
    {
        return $this->recipients;
    }

    public function getContext(): string
    {
        return $this->context;
    }
}
