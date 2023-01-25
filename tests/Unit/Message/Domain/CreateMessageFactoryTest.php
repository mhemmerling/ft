<?php

declare(strict_types=1);

namespace App\Tests\Unit\Message\Domain;

use App\Message\Command\CreateMessageFactory;
use App\Shared\Exception\InvalidMessageContextException;
use App\User\Domain\User;
use App\Message\Command\CreateMessage;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class CreateMessageFactoryTest extends TestCase
{
    public function testCreateMessage(): void
    {
        $request = $this->createMock(Request::class);
        $request->method('getContent')->willReturn('{"title": "sampleTitle", "content": "sampleContent", "recipients": [1,2,3]}');
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(23);

        $validator = $this->createMock(ValidatorInterface::class);
        $validatorViolations = $this->createMock(ConstraintViolationListInterface::class);
        $validator->method('validate')->willReturn($validatorViolations);
        $validatorViolations->method('count')->willReturn(0);
        $messageFactory = new CreateMessageFactory(
            $validator
        );

        $message = $messageFactory->createFromApiRequest($request, $user);

        self::assertEquals('sampleTitle', $message->getTitle());
        self::assertEquals('sampleContent', $message->getContent());
        self::assertEquals(23, $message->getSender()->getId());
        self::assertEquals('USER', $message->getContext());
        self::assertEquals([1,2,3], $message->getRecipients());
    }

    public function testCreateMessageWithContext(): void
    {
        $request = $this->createMock(Request::class);
        $request->method('getContent')->willReturn('{"title": "sampleTitle", "context": "SYSTEM", "content": "sampleContent", "recipients": [1,2,3]}');
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(23);

        $validator = $this->createMock(ValidatorInterface::class);
        $validatorViolations = $this->createMock(ConstraintViolationListInterface::class);
        $validator->method('validate')->willReturn($validatorViolations);
        $validatorViolations->method('count')->willReturn(0);
        $messageFactory = new CreateMessageFactory(
            $validator
        );

        $message = $messageFactory->createFromApiRequest($request, $user);
        $expected = new CreateMessage(
            'sampleTitle',
            'sampleContent',
            $user,
            'SYSTEM',
            [1,2,3],
        );

        self::assertEquals($expected, $message);
    }

    public function testCreateMessageWithInvalidContext(): void
    {
        self::expectException(InvalidMessageContextException::class);

        $request = $this->createMock(Request::class);
        $request->method('getContent')->willReturn('{"title": "sampleTitle", "context": "$%^#%@", "content": "sampleContent", "recipients": [1,2,3]}');
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(23);
        $validator = $this->createMock(ValidatorInterface::class);
        $validatorViolations = $this->createMock(ConstraintViolationListInterface::class);
        $validator->method('validate')->willReturn($validatorViolations);
        $validatorViolations->method('count')->willReturn(0);
        $messageFactory = new CreateMessageFactory(
            $validator
        );

        $messageFactory->createFromApiRequest($request, $user);
    }

}