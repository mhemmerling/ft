<?php

declare(strict_types=1);

namespace App\Message\Domain;

use App\Message\Infrastructure\MessageRepository;
use App\User\Domain\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    private string $title;

    #[ORM\Column(length: 100)]
    #[Assert\Length(max: 100)]
    private ?string $content = null;

    #[ORM\OneToMany(
        mappedBy: 'message',
        targetEntity: MessageRecipient::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true,
    )]
    private Collection $messageRecipients;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt;

    #[ORM\Column(length: 64)]
    private string $context;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?UserInterface $sender = null;

    public function __construct()
    {
        $this->messageRecipients = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return Collection<int, MessageRecipient>
     */
    public function getMessageRecipients(): Collection
    {
        return $this->messageRecipients;
    }

    public function addMessageRecipient(MessageRecipient $messageRecipient): self
    {
        if (!$this->messageRecipients->contains($messageRecipient)) {
            $this->messageRecipients->add($messageRecipient);
            $messageRecipient->setMessage($this);
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getContext(): ?string
    {
        return $this->context;
    }

    public function setContext(string $context): self
    {
        $this->context = $context;

        return $this;
    }

    public function getSender(): ?UserInterface
    {
        return $this->sender;
    }

    public function setSender(?UserInterface $sender): self
    {
        $this->sender = $sender;

        return $this;
    }
}
