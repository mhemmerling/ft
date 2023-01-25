<?php

declare(strict_types=1);

namespace App\Tests\Unit\Message\Command;

use App\Message\Command\DeleteMessage;
use App\User\Domain\User;
use PHPUnit\Framework\TestCase;

class DeleteMessageTest extends TestCase
{

    public function testShouldCreateDeleteCommand(): void
    {
        $user = new User();
        $message = new DeleteMessage(1, $user);
        self::assertEquals(1, $message->getId());
        self::assertEquals($user, $message->getUser());
    }

}
