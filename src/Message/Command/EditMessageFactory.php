<?php

declare(strict_types=1);

namespace App\Message\Command;

use App\Shared\Exception\InvalidInput;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class EditMessageFactory
{
    public function __construct(
        private readonly ValidatorInterface $validator
    ) {
    }

    public function createFromApiRequest(
        int $id,
        Request $request,
        UserInterface $user
    ): EditMessage {
        $input = json_decode($request->getContent(), true);
        $constraints = new Assert\Collection([
            'title' => [new Assert\Length(['min' => 3, 'max' => 255]), new Assert\NotBlank()],
            'content' => [new Assert\Length(['max' => 255]), new Assert\NotBlank()]
        ]);

        if ($this->validator->validate($input, $constraints)->count() > 0) {
            throw new InvalidInput('Invalid input');
        }

        return new EditMessage(
            $id,
            $user,
            $input['title'],
            $input['content'],
        );
    }
}
