<?php

declare(strict_types=1);

namespace App\Message\Infrastructure\Controller;

use App\Message\Application\MessageServiceInterface;
use App\User\Application\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class MessageWebController extends AbstractController
{
    public function __construct(
        private readonly UserServiceInterface $userService,
        private readonly MessageServiceInterface $messageService
    ) {
    }

    public function send(): Response
    {
        return $this->render(
            'message/send.html.twig',
            [
                'users' => $this->userService->getUsers(),
            ]
        );
    }

    public function edit(int $id): Response
    {
        try {
            return $this->render(
                'message/edit.html.twig',
                [
                    'id' => $id,
                    'message' => $this->messageService->getMessage($id, $this->getUser()->getId()),
                ]
            );
        } catch (\Throwable $e) {
            return new Response('Message not found', Response::HTTP_NOT_FOUND);
        }
    }

    public function sent(): Response
    {
        return $this->render(
            'message/sent.html.twig',
            [
                'messages' => $this->messageService->getSentMessages($this->getUser()->getId()),
            ]
        );
    }

    public function list(): Response
    {
        return $this->render(
            'message/list.html.twig',
            [
                'messages' => $this->messageService->listUserMessages(
                    $this->getUser()->getId()
                ),
            ]
        );
    }
}
