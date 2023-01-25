<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Message\Infrastructure\MessageRepository;
use App\User\Domain\User;
use App\User\Infrastructure\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MessageWebControllerTest extends WebTestCase
{
    private const TEST_USER_EMAIL = 'integrationtest@example.org';

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::bootKernel();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $userRepository->save(
            (new User())
                ->setEmail(self::TEST_USER_EMAIL)
                ->setPassword('password')
                ->setRoles(['ROLE_USER']),
            true
        );
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $userRepository->remove(
            $userRepository->findOneBy(['email' => self::TEST_USER_EMAIL]),
            true
        );
    }

    public function setUp(): void
    {
        parent::setUp();

        self::ensureKernelShutdown();
        $this->client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail(self::TEST_USER_EMAIL);
        $this->client->loginUser($testUser);
    }

    public function testMessagesForm(): void
    {
        $this->client->request('GET', '/messages/send');
        $this->assertResponseIsSuccessful();
    }

    public function testMessagesList(): void
    {
        $this->client->request('GET', '/messages');
        $this->assertResponseIsSuccessful();
    }

    public function testSentMessagesList(): void
    {
        $this->client->request('GET', '/messages/sent');
        $this->assertResponseIsSuccessful();
    }

    public function testEditNotOwnedMessage(): void
    {
        $this->client->request('GET', '/messages/edit/' . PHP_INT_MAX);
        $this->assertResponseStatusCodeSame(404);
    }
}
