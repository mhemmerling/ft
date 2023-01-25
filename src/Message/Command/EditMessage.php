<?php

declare(strict_types=1);

namespace App\Message\Command;

use Symfony\Component\Security\Core\User\UserInterface;

final class EditMessage
{
    public function __construct(
        private readonly int $id,
        private readonly UserInterface $user,
        private readonly string $title,
        private readonly string $content
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
