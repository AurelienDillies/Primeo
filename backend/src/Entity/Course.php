<?php

namespace App\Entity;

use App\Repository\CourseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: CourseRepository::class)]
class Course
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['student:read', 'teacher:read', 'classe:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Groups(['student:read', 'teacher:read', 'classe:read'])]
    private ?string $courseTitle = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['student:read', 'teacher:read', 'classe:read'])]
    private ?string $courseDescription = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['student:read', 'teacher:read', 'classe:read'])]
    private ?string $courseResourcefile = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['student:read', 'teacher:read', 'classe:read'])]
    private ?string $courseVideoUrl = null;

    #[ORM\ManyToOne(inversedBy: 'courses')]
    private ?Classe $classe = null;

    /**
     * @var Collection<int, Activity>
     */
    #[ORM\OneToMany(targetEntity: Activity::class, mappedBy: 'course', cascade: ['persist', 'remove'])]
    #[Groups(['student:read', 'teacher:read', 'classe:read'])]
    private Collection $activities;

    /**
     * @var Collection<int, Progress>
     */
    #[ORM\OneToMany(targetEntity: Progress::class, mappedBy: 'course', cascade: ['persist', 'remove'])]
    #[Groups(['student:read', 'teacher:read'])]
    private Collection $progresses;

    /**
     * @var Collection<int, Report>
     */
    #[ORM\OneToMany(targetEntity: Report::class, mappedBy: 'course', cascade: ['persist', 'remove'])]
    #[Groups(['student:read', 'teacher:read', 'classe:read'])]
    private Collection $reports;

    public function __construct()
    {
        $this->activities = new ArrayCollection();
        $this->progresses = new ArrayCollection();
        $this->reports = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCourseTitle(): ?string
    {
        return $this->courseTitle;
    }

    public function setCourseTitle(string $courseTitle): static
    {
        $this->courseTitle = $courseTitle;

        return $this;
    }

    public function getCourseDescription(): ?string
    {
        return $this->courseDescription;
    }

    public function setCourseDescription(string $courseDescription): static
    {
        $this->courseDescription = $courseDescription;

        return $this;
    }

    public function getCourseResourcefile(): ?string
    {
        return $this->courseResourcefile;
    }

    public function setCourseResourcefile(?string $courseResourcefile): static
    {
        $this->courseResourcefile = $courseResourcefile;

        return $this;
    }

    public function getCourseVideoUrl(): ?string
    {
        return $this->courseVideoUrl;
    }

    public function setCourseVideoUrl(?string $courseVideoUrl): static
    {
        $this->courseVideoUrl = $courseVideoUrl;

        return $this;
    }

    public function getClasse(): ?Classe
    {
        return $this->classe;
    }

    public function setClasse(?Classe $classe): static
    {
        $this->classe = $classe;

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
            $activity->setCourse($this);
        }

        return $this;
    }

    public function removeActivity(Activity $activity): static
    {
        if ($this->activities->removeElement($activity)) {
            // set the owning side to null (unless already changed)
            if ($activity->getCourse() === $this) {
                $activity->setCourse(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Progress>
     */
    public function getProgresses(): Collection
    {
        return $this->progresses;
    }

    public function addProgress(Progress $progress): static
    {
        if (!$this->progresses->contains($progress)) {
            $this->progresses->add($progress);
            $progress->setCourse($this);
        }

        return $this;
    }

    public function removeProgress(Progress $progress): static
    {
        if ($this->progresses->removeElement($progress)) {
            // set the owning side to null (unless already changed)
            if ($progress->getCourse() === $this) {
                $progress->setCourse(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Report>
     */
    public function getReports(): Collection
    {
        return $this->reports;
    }

    public function addReport(Report $report): static
    {
        if (!$this->reports->contains($report)) {
            $this->reports->add($report);
            $report->setCourse($this);
        }

        return $this;
    }

    public function removeReport(Report $report): static
    {
        if ($this->reports->removeElement($report)) {
            // set the owning side to null (unless already changed)
            if ($report->getCourse() === $this) {
                $report->setCourse(null);
            }
        }

        return $this;
    }
}
