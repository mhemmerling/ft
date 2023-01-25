<?php

declare(strict_types=1);

namespace App\Tests\Unit\Message\Domain;

use App\Message\Command\EditMessage;
use App\Message\Command\EditMessageFactory;
use App\Shared\Exception\InvalidInput;
use App\User\Domain\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class EditMessageFactoryTest extends TestCase
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
        $messageFactory = new EditMessageFactory(
            $validator
        );

        $message = $messageFactory->createFromApiRequest(1, $request, $user);

        self::assertEquals('sampleTitle', $message->getTitle());
        self::assertEquals('sampleContent', $message->getContent());
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
        $messageFactory = new EditMessageFactory(
            $validator
        );

        $message = $messageFactory->createFromApiRequest(1, $request, $user);
        $expected = new EditMessage(
            1,
            $user,
            'sampleTitle',
            'sampleContent'
        );

        self::assertEquals($expected, $message);
    }

    public function testCreateMessageWithInvalidContent(): void
    {
        $this->expectException(InvalidInput::class);

        $request = $this->createMock(Request::class);
        $request->method('getContent')->willReturn('{"title": "s", "content": "sa"}');
        $user = $this->createMock(User::class);
        $validator = $this->createMock(ValidatorInterface::class);
        $validatorViolations = $this->createMock(ConstraintViolationListInterface::class);
        $validator->method('validate')->willReturn($validatorViolations);
        $validatorViolations->method('count')->willReturn(1);
        $messageFactory = new EditMessageFactory(
            $validator
        );

        $messageFactory->createFromApiRequest(1, $request, $user);
    }

}