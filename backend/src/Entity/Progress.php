<?php

namespace App\Entity;

use App\Repository\ProgressRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ProgressRepository::class)]
class Progress
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['student:read', 'teacher:read'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['student:read', 'teacher:read'])]
    private ?float $progressPercent = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['student:read', 'teacher:read'])]
    private ?string $progressGrade = null;

    #[ORM\ManyToOne(inversedBy: 'progresses', cascade: ['persist', 'remove'])]
    #[Groups(['teacher:read'])]
    private ?Student $student = null;

    #[ORM\ManyToOne(inversedBy: 'progresses', cascade: ['persist', 'remove'])]
    
    private ?Course $course = null;

    /**
     * @var Collection<int, Activity>
     */
    #[ORM\OneToMany(targetEntity: Activity::class, mappedBy: 'progress', cascade: ['persist', 'remove'])]
    private Collection $activities;

    public function __construct()
    {
        $this->activities = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProgressPercent(): ?float
    {
        return $this->progressPercent;
    }

    public function setProgressPercent(float $progressPercent): static
    {
        $this->progressPercent = $progressPercent;

        return $this;
    }

    public function getProgressGrade(): ?string
    {
        return $this->progressGrade;
    }

    public function setProgressGrade(?string $progressGrade): static
    {
        $this->progressGrade = $progressGrade;

        return $this;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): static
    {
        $this->student = $student;

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

    /**
     * @return Collection<int, Activity>
     */
    public function getActivities(): Collection
    {
        return $this->activities;
    }

    public function addActivity(Activity $activity): static
    {
        if (!$this->activities->contains($activity)) {
            $this->activities->add($activity);
            $activity->setProgress($this);
        }

        return $this;
    }

    public function removeActivity(Activity $activity): static
    {
        if ($this->activities->removeElement($activity)) {
            // set the owning side to null (unless already changed)
            if ($activity->getProgress() === $this) {
                $activity->setProgress(null);
            }
        }

        return $this;
    }
}
