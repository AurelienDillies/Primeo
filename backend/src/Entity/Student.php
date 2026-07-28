<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity]
class Student extends User
{
    #[ORM\Column(type: 'date')]
    #[Groups(['student:read', 'classe:read'])]
    private ?\DateTimeInterface $enrollmentDate = null;

    /**
     * @var Collection<int, Classe>
     */
    #[ORM\ManyToMany(targetEntity: Classe::class, inversedBy: 'students')]
    #[Groups(['student:read'])]
    private Collection $classes;

    /**
     * @var Collection<int, Parents>
     */
    #[ORM\ManyToMany(targetEntity: Parents::class, mappedBy: 'children')]
    #[Groups(['student:read'])]
    private Collection $parents;

    #[ORM\Column(type: Types::DECIMAL, precision: 3, scale: 2, nullable: true)]
    private ?string $pourcent = null;

    public function __construct()
    {
        parent::__construct();
        $this->classes = new ArrayCollection();
        $this->parents = new ArrayCollection();
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
     * @return Collection<int, Parents>
     */
    public function getParents(): Collection
    {
        return $this->parents;
    }

    public function addParent(Parents $parent): static
    {
        if (!$this->parents->contains($parent)) {
            $this->parents->add($parent);
            $parent->addChild($this);
        }

        return $this;
    }

    public function removeParent(Parents $parent): static
    {
        if ($this->parents->removeElement($parent)) {
            $parent->removeChild($this);
        }

        return $this;
    }

    public function getPourcent(): ?string
    {
        return $this->pourcent;
    }

    public function setPourcent(?string $pourcent): static
    {
        $this->pourcent = $pourcent;

        return $this;
    }
}
