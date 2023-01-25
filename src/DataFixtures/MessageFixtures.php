<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Message\Domain\Message;
use App\Message\Domain\MessageContext;
use App\Message\Domain\MessageRecipient;
use App\User\Domain\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class MessageFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $users = $manager->getRepository(User::class)->findAll();

        for ($i = 1; $i <= 100; ++$i) {
            $manager->persist(
                (new Message())
                    ->setTitle($faker->realText(20))
                    ->setContent($faker->realText(90))
                    ->setSender($faker->randomElement($users))
                    ->setContext(MessageContext::getContext($faker->randomElement(['USER', 'SYSTEM'])))
                    ->addMessageRecipient(
                        (new MessageRecipient())->setUser($faker->randomElement($users))
                    )
            );
        }

        $manager->flush();
    }

    public function getOrder(): int
    {
        return 2;
    }
}
