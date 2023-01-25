<?php

declare(strict_types=1);

namespace App\Message\Domain;

use App\Message\Infrastructure\MessageRecipientRepository;
use App\User\Domain\User;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRecipientRepository::class)]
class MessageRecipient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Message::class, inversedBy: 'messageRecipients')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private Message $message;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column]
    private bool $isRead = false;

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): Message
    {
        return $this->message;
    }

    public function setMessage(Message $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function isIsRead(): bool
    {
        return $this->isRead;
    }

    public function setIsRead(bool $isRead): self
    {
        $this->isRead = $isRead;

        return $this;
    }
}
