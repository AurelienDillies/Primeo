<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Student extends User
{
    #[ORM\Column(type: "date")]
    private ?\DateTimeInterface $enrollmentDate = null;

    public function getEnrollmentDate(): ?\DateTimeInterface
    {
        return $this->enrollmentDate;
    }

    public function setEnrollmentDate(?\DateTimeInterface $enrollmentDate): self
    {
        $this->enrollmentDate = $enrollmentDate;

        return $this;
    }
}
