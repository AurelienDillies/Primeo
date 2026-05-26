<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $MessageContent = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $MessageSent = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessageContent(): ?string
    {
        return $this->MessageContent;
    }

    public function setMessageContent(string $MessageContent): static
    {
        $this->MessageContent = $MessageContent;

        return $this;
    }

    public function getMessageSent(): ?\DateTimeImmutable
    {
        return $this->MessageSent;
    }

    public function setMessageSent(\DateTimeImmutable $MessageSent): static
    {
        $this->MessageSent = $MessageSent;

        return $this;
    }
}
