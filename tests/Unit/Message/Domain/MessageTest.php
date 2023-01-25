<?php

declare(strict_types=1);

namespace App\Tests\Unit\Message\Domain;

use App\Message\Domain\Message;
use App\User\Domain\User;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    public function testCreatingMessage(): void
    {
        $user = $this->createMock(User::class);

        $message = new Message();
        $message->setId(123);
        $message->setTitle('title');
        $message->setContent('content');
        $message->setContext('USER');
        $message->setSender($user);

        self::assertEquals(123, $message->getId());
        self::assertEquals('title', $message->getTitle());
        self::assertEquals('content', $message->getContent());
        self::assertEquals('USER', $message->getContext());
        self::assertEquals($user, $message->getSender());
    }
}