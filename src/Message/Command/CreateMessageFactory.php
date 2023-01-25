<?php

declare(strict_types=1);

namespace App\Message\Command;

use App\Message\Domain\MessageContext;
use App\Shared\Exception\InvalidInput;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class CreateMessageFactory
{
    public function __construct(
        private readonly ValidatorInterface $validator
    ) {
    }

    public function createFromApiRequest(Request $request, UserInterface $user): CreateMessage
    {
        $input = json_decode($request->getContent(), true);
        $constraints = new Assert\Collection([
            'title' => [new Assert\Length(['min' => 3, 'max' => 255]), new Assert\NotBlank()],
            'content' => [new Assert\Length(['max' => 100]), new Assert\NotBlank()],
            'recipients' => [new Assert\Count(['min' => 1]), new Assert\NotBlank()],
        ]);

        if ($this->validator->validate($input, $constraints)->count() > 0) {
            throw new InvalidInput('Invalid input');
        }

        return new CreateMessage(
            $input['title'],
            $input['content'],
            $user,
            isset($input['context'])
                ? MessageContext::getContext($input['context'])
                : MessageContext::USER->value,
            $input['recipients']
        );
    }
}
