<?php

declare(strict_types=1);

namespace App\Message\Application;

use App\Message\Command\ChangeMessageStatus;
use App\Message\Command\CreateMessage;
use App\Message\Command\DeleteMessage;
use App\Message\Command\EditMessage;
use App\Message\Domain\Message;
use App\Message\Domain\MessageRecipient;
use App\Message\Infrastructure\MessageRecipientRepository;
use App\Message\Infrastructure\MessageRepository;
use App\Shared\Exception\NoRecipientsException;
use App\Shared\Exception\RecipientNotFound;
use App\User\Infrastructure\UserRepository;
use Symfony\Component\Messenger\MessageBusInterface;

final class MessageService implements MessageServiceInterface
{
    public function __construct(
        private readonly MessageRepository $messageRepository,
        private readonly MessageRecipientRepository $messageRecipientRepository,
        private readonly UserRepository $userRepository,
        private readonly MessageBusInterface $eventBus
    ) {
    }

    public function send(CreateMessage $command): void
    {
        if (empty($command->getRecipients())) {
            throw new NoRecipientsException();
        }

        $message = new Message();
        $message->setTitle($command->getTitle());
        $message->setContent($command->getContent());
        $message->setContext($command->getContext());
        $message->setSender($command->getSender());

        foreach ($command->getRecipients() as $recipient) {
            $user = $this->userRepository->find((int) $recipient);

            if (!$user) {
                throw new RecipientNotFound($recipient);
            }

            $message->addMessageRecipient(
                (new MessageRecipient())->setUser($user)
            );
        }

        $this->messageRepository->save($message, true);
        $this->eventBus->dispatch(new MessageSentEvent($message));
    }

    public function deleteRecipient(DeleteMessage $command): void
    {
        $this->messageRecipientRepository->deleteRecipient($command);
    }

    public function delete(DeleteMessage $command): void
    {
        $this->messageRepository->delete($command);
    }

    public function setReadStatus(ChangeMessageStatus $command): void
    {
        $this->messageRecipientRepository->changeStatus($command);
    }

    public function listUserMessages(int $userId): array
    {
        return $this->messageRepository->listForUser($userId);
    }

    public function getSentMessages(int $userId): array
    {
        return $this->messageRepository->listSentMessages($userId);
    }

    public function getMessage(int $id, int $userId): Message
    {
        return $this->messageRepository->findOneBy(['id' => $id, 'sender' => $userId]);
    }

    public function edit(EditMessage $command): void
    {
        $message = $this->messageRepository->findOneBy(
            ['id' => $command->getId(), 'sender' => $command->getUser()->getId()]
        );

        $message->setTitle($command->getTitle());
        $message->setContent($command->getContent());

        $this->messageRepository->save($message, true);
    }
}
