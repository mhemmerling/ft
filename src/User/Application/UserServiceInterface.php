<?php

declare(strict_types=1);

namespace App\User\Application;

use App\User\Command\CreateUser;

interface UserServiceInterface
{
    public function getUsers(): array;

    public function createUser(CreateUser $command): void;
}
