<?php

declare(strict_types=1);

namespace App\Message\Infrastructure\Controller;

use App\Message\Application\MessageServiceInterface;
use App\Message\Command\ChangeMessageStatus;
use App\Message\Command\CreateMessageFactory;
use App\Message\Command\DeleteMessage;
use App\Message\Command\EditMessageFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class MessageApiController extends AbstractController
{
    public function __construct(
        private readonly MessageServiceInterface $messageService,
        private readonly CreateMessageFactory $createMessageFactory,
        private readonly EditMessageFactory $editMessageFactory
    ) {
    }

    public function send(Request $request): JsonResponse
    {
        try {
            $this->messageService->send(
                $this->createMessageFactory->createFromApiRequest($request, $this->getUser())
            );

            return new JsonResponse(null, Response::HTTP_ACCEPTED);
        } catch (\Throwable $e) {
            return new JsonResponse(
                ['error' => $e->getMessage(), 'code' => $e->getCode()],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    public function edit(int $id, Request $request): JsonResponse
    {
        try {
            $this->messageService->edit(
                $this->editMessageFactory->createFromApiRequest($id, $request, $this->getUser())
            );

            return new JsonResponse(null, Response::HTTP_ACCEPTED);
        } catch (\Throwable $e) {
            return new JsonResponse(
                ['error' => $e->getMessage(), 'code' => $e->getCode()],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    public function delete(int $id): JsonResponse
    {
        $this->messageService->delete(
            new DeleteMessage($id, $this->getUser())
        );

        return new JsonResponse(null, Response::HTTP_ACCEPTED);
    }

    public function deleteByAuthor(int $id): JsonResponse
    {
        $this->messageService->delete(
            new DeleteMessage($id, $this->getUser())
        );

        return new JsonResponse(null, Response::HTTP_ACCEPTED);
    }

    public function read(int $id): JsonResponse
    {
        return $this->setStatus($id, true);
    }

    public function unread(int $id): JsonResponse
    {
        return $this->setStatus($id, false);
    }

    private function setStatus(int $id, bool $read): JsonResponse
    {
        $this->messageService->setReadStatus(
            new ChangeMessageStatus(
                $this->getUser()->getId(),
                $id,
                $read
            )
        );

        return new JsonResponse(null, Response::HTTP_ACCEPTED);
    }
}
