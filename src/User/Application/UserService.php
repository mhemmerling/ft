<?php

declare(strict_types=1);

namespace App\User\Application;

use App\User\Command\CreateUser;
use App\User\Domain\User;
use App\User\Infrastructure\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserService implements UserServiceInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function getUsers(): array
    {
        return $this->userRepository->findAll();
    }

    public function createUser(CreateUser $command): void
    {
        $user = new User();
        $user->setEmail($command->getEmail());
        $user->setRoles(['ROLE_USER']);
        $user->setPassword(
            $this->passwordHasher->hashPassword(
                $user,
                $command->getPassword()
            )
        );
        $this->userRepository->save($user, true);
    }
}
