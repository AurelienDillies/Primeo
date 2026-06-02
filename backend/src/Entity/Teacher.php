<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Teacher extends User
{
    #[ORM\Column(length: 255)]
    private ?string $subject = null;
}
