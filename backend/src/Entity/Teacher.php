<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity]
class Teacher extends User
{
    #[ORM\Column(length: 255)]
    #[Groups(['teacher:read', 'classe:read'])]
    private ?string $subject = null;

    /**
     * @var Collection<int, Classe>
     */
    #[ORM\OneToMany(targetEntity: Classe::class, mappedBy: 'teacher', cascade: ['persist', 'remove'])]
    private Collection $teachingClasses;

    public function __construct()
    {
        parent::__construct();
        $this->teachingClasses = new ArrayCollection();
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @return Collection<int, Classe>
     */
    public function getTeachingClasses(): Collection
    {
        return $this->teachingClasses;
    }

    public function addTeachingClass(Classe $teachingClass): static
    {
        if (!$this->teachingClasses->contains($teachingClass)) {
            $this->teachingClasses->add($teachingClass);
            $teachingClass->setTeacher($this);
        }

        return $this;
    }

    public function removeTeachingClass(Classe $teachingClass): static
    {
        if ($this->teachingClasses->removeElement($teachingClass)) {
            if ($teachingClass->getTeacher() === $this) {
                $teachingClass->setTeacher(null);
            }
        }

        return $this;
    }
}
