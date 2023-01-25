<?php

declare(strict_types=1);

namespace App\Message\Command;

use Symfony\Component\Security\Core\User\UserInterface;

final class DeleteMessage
{
    public function __construct(
        private readonly int $id,
        private readonly UserInterface $user
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
}
