<?php

declare(strict_types=1);

namespace App\Tests\Unit\Message\Domain;

use App\Message\Domain\Message;
use App\Message\Domain\MessageRecipient;
use App\User\Domain\User;
use PHPUnit\Framework\TestCase;

class MessageRecipientTest extends TestCase
{
    public function testCreatingRecipient(): void
    {
        $message = $this->createMock(Message::class);
        $user = $this->createMock(User::class);

        $recipient = new MessageRecipient();
        $recipient->setId(123);
        $recipient->setIsRead(true);
        $recipient->setMessage($message);
        $recipient->setUser($user);

        self::assertEquals(123, $recipient->getId());
        self::assertEquals($message, $recipient->getMessage());
        self::assertEquals($user, $recipient->getUser());
        self::assertEquals(true, $recipient->isIsRead());

    }
}