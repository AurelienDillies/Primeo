<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Student extends User
{
    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $enrollmentDate = null;

    /**
     * @var Collection<int, Classe>
     */
    #[ORM\ManyToMany(targetEntity: Classe::class, inversedBy: 'students')]
    private Collection $classes;

    /**
     * @var Collection<int, Progress>
     */
    #[ORM\OneToMany(targetEntity: Progress::class, mappedBy: 'student')]
    private Collection $progresses;

    public function __construct()
    {
        parent::__construct();
        $this->classes = new ArrayCollection();
        $this->progresses = new ArrayCollection();
    }

    public function getEnrollmentDate(): ?\DateTimeInterface
    {
        return $this->enrollmentDate;
    }

    public function setEnrollmentDate(?\DateTimeInterface $enrollmentDate): self
    {
        $this->enrollmentDate = $enrollmentDate;

        return $this;
    }

    /**
     * @return Collection<int, Classe>
     */
    public function getClasses(): Collection
    {
        return $this->classes;
    }

    public function addClass(Classe $class): static
    {
        if (!$this->classes->contains($class)) {
            $this->classes->add($class);
            $class->addStudent($this);
        }

        return $this;
    }

    public function removeClass(Classe $class): static
    {
        if ($this->classes->removeElement($class)) {
            $class->removeStudent($this);
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
            $progress->setStudent($this);
        }

        return $this;
    }

    public function removeProgress(Progress $progress): static
    {
        if ($this->progresses->removeElement($progress)) {
            if ($progress->getStudent() === $this) {
                $progress->setStudent(null);
            }
        }

        return $this;
    }
}
