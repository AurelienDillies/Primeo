<?php

namespace App\Entity;

use App\Repository\ActivityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ActivityRepository::class)]
class Activity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['student:read', 'teacher:read', 'classe:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Groups(['student:read', 'teacher:read', 'classe:read'])]
    private ?string $activityType = null;

    #[ORM\Column(length: 100)]
    #[Groups(['student:read', 'teacher:read', 'classe:read'])]
    private ?string $activityTitle = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['student:read', 'teacher:read', 'classe:read'])]
    private ?string $activityDescription = null;

    #[ORM\Column]
    #[Groups(['student:read', 'teacher:read', 'classe:read'])]
    private ?\DateTimeImmutable $activityDate = null;

    #[ORM\ManyToOne(inversedBy: 'activities')]
    private ?Course $course = null;

    #[ORM\ManyToOne(inversedBy: 'activities')]
    #[Groups(['user:read', 'teacher:read', 'student:read'])]
    private ?Progress $progress = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getActivityType(): ?string
    {
        return $this->activityType;
    }

    public function setActivityType(string $activityType): static
    {
        $this->activityType = $activityType;

        return $this;
    }

    public function getActivityTitle(): ?string
    {
        return $this->activityTitle;
    }

    public function setActivityTitle(string $activityTitle): static
    {
        $this->activityTitle = $activityTitle;

        return $this;
    }

    public function getActivityDescription(): ?string
    {
        return $this->activityDescription;
    }

    public function setActivityDescription(?string $activityDescription): static
    {
        $this->activityDescription = $activityDescription;

        return $this;
    }

    public function getActivityDate(): ?\DateTimeImmutable
    {
        return $this->activityDate;
    }

    public function setActivityDate(\DateTimeImmutable $activityDate): static
    {
        $this->activityDate = $activityDate;

        return $this;
    }

    public function getCourse(): ?Course
    {
        return $this->course;
    }

    public function setCourse(?Course $course): static
    {
        $this->course = $course;

        return $this;
    }

    public function getProgress(): ?Progress
    {
        return $this->progress;
    }

    public function setProgress(?Progress $progress): static
    {
        $this->progress = $progress;

        return $this;
    }
}
