<?php

namespace App\Entity;

use App\Repository\AttachmentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AttachmentRepository::class)]
class Attachment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $attachmentName = null;

    #[ORM\Column(length: 255)]
    private ?string $attachmentPath = null;

    #[ORM\Column(length: 100)]
    private ?string $attachmentType = null;

    #[ORM\Column]
    private ?int $attachmentSize = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $attachmentCreatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'attachments')]
    private ?Message $message = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAttachmentName(): ?string
    {
        return $this->attachmentName;
    }

    public function setAttachmentName(string $attachmentName): static
    {
        $this->attachmentName = $attachmentName;

        return $this;
    }

    public function getAttachmentPath(): ?string
    {
        return $this->attachmentPath;
    }

    public function setAttachmentPath(string $attachmentPath): static
    {
        $this->attachmentPath = $attachmentPath;

        return $this;
    }

    public function getAttachmentType(): ?string
    {
        return $this->attachmentType;
    }

    public function setAttachmentType(string $attachmentType): static
    {
        $this->attachmentType = $attachmentType;

        return $this;
    }

    public function getAttachmentSize(): ?int
    {
        return $this->attachmentSize;
    }

    public function setAttachmentSize(int $attachmentSize): static
    {
        $this->attachmentSize = $attachmentSize;

        return $this;
    }

    public function getAttachmentCreatedAt(): ?\DateTimeImmutable
    {
        return $this->attachmentCreatedAt;
    }

    public function setAttachmentCreatedAt(\DateTimeImmutable $attachmentCreatedAt): static
    {
        $this->attachmentCreatedAt = $attachmentCreatedAt;

        return $this;
    }

    public function getMessage(): ?Message
    {
        return $this->message;
    }

    public function setMessage(?Message $message): static
    {
        $this->message = $message;

        return $this;
    }
}
