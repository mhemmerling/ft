<?php

namespace App\Tests\Unit\Message\Application;

use App\Message\Command\DeleteMessage;
use App\User\Domain\User;
use App\Message\Application\MessageService;
use App\Message\Command\CreateMessage;
use App\Message\Command\EditMessage;
use App\Message\Domain\Message;
use App\Message\Infrastructure\MessageRecipientRepository;
use App\Message\Infrastructure\MessageRepository;
use App\Shared\Exception\NoRecipientsException;
use App\Shared\Exception\RecipientNotFound;
use App\User\Infrastructure\UserRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class MessageServiceTest extends TestCase
{
    private UserRepository $userRepository;
    private MessageRepository $messageRepository;
    private MessageRecipientRepository $messageRecipientRepository;
    private MessageService $messageService;
    private MessageBusInterface $messageBus;

    public function setUp(): void
    {
        parent::setUp();
        $this->messageRepository = $this->createMock(MessageRepository::class);
        $this->messageRecipientRepository = $this->createMock(MessageRecipientRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->messageBus = $this->createMock(MessageBusInterface::class);
        $this->messageService = new MessageService(
            $this->messageRepository,
            $this->messageRecipientRepository,
            $this->userRepository,
            $this->messageBus
        );
    }

    public function testShouldSendMessage(): void
    {
        $message = new CreateMessage(
            'title',
            'content',
            new User(),
            'USER',
            [1,2,3]
        );

        $this->userRepository->method('find')->willReturn(new User());
        $this->messageBus
            ->expects($this->once())
            ->method('dispatch')
            ->willReturn(new Envelope(new \stdClass()));
        $this->messageRepository->expects($this->once())->method('save');

        $this->messageService->send($message);
    }

    public function testShouldThrowNoRecipientsException(): void
    {
        self::expectException(NoRecipientsException::class);

        $message = new CreateMessage(
            'title',
            'content',
            new User(),
            'USER',
            []
        );

        $this->messageService->send($message);
    }

    public function testShouldThrowRecipientNotFoundException(): void
    {
        self::expectException(RecipientNotFound::class);
        $this->userRepository->method('find')->willReturn(null);
        $message = new CreateMessage(
            'title',
            'content',
            new User(),
            'USER',
            [20]
        );

        $this->messageService->send($message);
    }

    public function testShouldReturnArrayFromRepository(): void
    {
        $this->messageRepository->method('listForUser')->willReturn([]);
        $this->messageRepository->expects($this->once())->method('listForUser');
        $this->messageService->listUserMessages(1);
    }

    public function testShouldDeleteRecipientMessage(): void
    {
        $this->messageRecipientRepository->expects($this->once())->method('deleteRecipient');
        $this->messageService->deleteRecipient(new DeleteMessage(1,new User()));
    }

    public function testShouldGetSingleMessage(): void
    {
        $message = new Message();
        $this->messageRepository->method('findOneBy')->willReturn($message);
        $this->messageRepository->expects($this->once())->method('findOneBy');

        self::assertEquals($message, $this->messageService->getMessage(1, 1));
    }

    public function testShouldEditMessage(): void
    {
        $message = new Message();
        $this->messageRepository->method('findOneBy')->willReturn($message);
        $this->messageRepository->expects($this->once())->method('findOneBy');
        $this->messageRepository->expects($this->once())->method('save');
        $this->messageService->edit(
            new EditMessage(1, new User(), 'title', 'content')
        );

        self::assertEquals('title', $message->getTitle());
        self::assertEquals('content', $message->getContent());
    }

    public function testShouldReturnSentMessages(): void
    {
        $this->messageRepository->method('listSentMessages')->willReturn(['test']);
        $this->messageRepository->expects($this->once())->method('listSentMessages');
        self::assertEquals(['test'], $this->messageService->getSentMessages(1));
    }
}
