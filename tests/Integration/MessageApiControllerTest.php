<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Message\Domain\MessageRecipient;
use App\Message\Infrastructure\MessageRepository;
use App\Message\Domain\Message;
use App\User\Domain\User;
use App\User\Infrastructure\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MessageApiControllerTest extends WebTestCase
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

    public function testAddMessageSuccess(): void
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $messageRepository = static::getContainer()->get(MessageRepository::class);
        $testUser = $userRepository->findOneByEmail(self::TEST_USER_EMAIL);

        self::assertEmpty($messageRepository->listForUser($testUser->getId()));

        $this->client->request(
            'POST', '/api/v1/messages', [], [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'title' => 'title',
                'content' => 'content',
                'recipients' => [$testUser->getId()],
            ])
        );

        $this->assertResponseStatusCodeSame(202);
        self::assertNotEmpty($messageRepository->listForUser($testUser->getId()));
    }

    public function testAddMessageFailure(): void
    {
        $this->client->request(
            'POST', '/api/v1/messages', [], [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode(['wrongdata' => 'causes failure'])
        );

        $this->assertResponseStatusCodeSame(400);
    }

    public function testMarkAsReadMessageSuccess(): void
    {
        $this->client->request('PUT', '/api/v1/messages/1/read');
        $this->assertResponseStatusCodeSame(202);
    }

    public function testMarkAsUnreadMessageSuccess(): void
    {
        $this->client->request('PUT', '/api/v1/messages/1/unread');
        $this->assertResponseStatusCodeSame(202);
    }


    public function testEditMessageSuccess(): void
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $messageRepository = static::getContainer()->get(MessageRepository::class);
        $testUser = $userRepository->findOneByEmail(self::TEST_USER_EMAIL);

        $messageRepository->save(
            (new Message())
                ->setTitle('title')
                ->setContent('content')
                ->setSender($testUser)
                ->setContext('SYSTEM')
                ->addMessageRecipient(
                    (new MessageRecipient())->setUser($testUser)
                ),
            true
        );

        $message = $messageRepository->listForUser($testUser->getId())[0];
        $this->client->request(
            'PUT', '/api/v1/messages/' . $message->getId(),
            [], [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'title' => 'title-edited',
                'content' => 'content-edited',
            ])
        );

        $this->assertResponseStatusCodeSame(202);

        $message = $messageRepository->listForUser($testUser->getId())[0];
        self::assertEquals('title-edited', $message->getTitle());
        self::assertEquals('content-edited', $message->getContent());
    }

    public function testEditMessageFailure(): void
    {
        $this->client->request(
            'PUT', '/api/v1/messages/' . PHP_INT_MAX,
            [], [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'title' => 'title-edited',
                'content' => 'content-edited',
            ])
        );

        $this->assertResponseStatusCodeSame(400);
    }

    public function testDeleteMessageSuccess(): void
    {
        $this->client->request('DELETE', '/api/v1/messages/1');
        $this->assertResponseStatusCodeSame(202);
    }

    public function testDeleteByAuthorMessageSuccess(): void
    {
        $this->client->request('DELETE', '/api/v1/messages/1/full');
        $this->assertResponseStatusCodeSame(202);
    }
}
