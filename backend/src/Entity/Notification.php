<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $NotificationMessage = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $NotificationDate = null;

    #[ORM\Column]
    private ?bool $NotificationLu = null;

    #[ORM\ManyToOne(inversedBy: 'notifications', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNotificationMessage(): ?string
    {
        return $this->NotificationMessage;
    }

    public function setNotificationMessage(string $NotificationMessage): static
    {
        $this->NotificationMessage = $NotificationMessage;

        return $this;
    }

    public function getNotificationDate(): ?\DateTimeImmutable
    {
        return $this->NotificationDate;
    }

    public function setNotificationDate(\DateTimeImmutable $NotificationDate): static
    {
        $this->NotificationDate = $NotificationDate;

        return $this;
    }

    public function isNotificationLu(): ?bool
    {
        return $this->NotificationLu;
    }

    public function setNotificationLu(bool $NotificationLu): static
    {
        $this->NotificationLu = $NotificationLu;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
