<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\User\Domain\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements OrderedFixtureInterface
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $testPassword = 'password';
        $faker = Factory::create();

        for ($i = 1; $i <= 20; ++$i) {
            $user = (new User())
                ->setEmail($faker->safeEmail)
                ->setRoles(['ROLE_USER']);
            $user->setPassword($this->passwordHasher->hashPassword($user, $testPassword));
            $manager->persist($user);
        }

        $manager->flush();
    }

    public function getOrder(): int
    {
        return 1;
    }
}
