<?php

namespace App\Entity;

use App\Repository\ReportRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ReportRepository::class)]
class Report
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['classe:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['classe:read'])]
    private ?string $reportType = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['classe:read'])]
    private ?string $reportData = null;

    #[ORM\Column]
    #[Groups(['classe:read'])]
    private ?\DateTimeImmutable $reportGeneratedAt = null;

    #[ORM\ManyToOne(inversedBy: 'reports', cascade: ['persist'])]    
    private ?Classe $classe = null;

    #[ORM\ManyToOne(inversedBy: 'reports', cascade: ['persist'])]    
    private ?Course $course = null;

    #[ORM\ManyToOne(inversedBy: 'reports', cascade: ['persist'])]
    #[Groups(['classe:read'])]
    private ?User $generatedBy = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReportType(): ?string
    {
        return $this->reportType;
    }

    public function setReportType(string $reportType): static
    {
        $this->reportType = $reportType;

        return $this;
    }

    public function getReportData(): ?string
    {
        return $this->reportData;
    }

    public function setReportData(string $reportData): static
    {
        $this->reportData = $reportData;

        return $this;
    }

    public function getReportGeneratedAt(): ?\DateTimeImmutable
    {
        return $this->reportGeneratedAt;
    }

    public function setReportGeneratedAt(\DateTimeImmutable $reportGeneratedAt): static
    {
        $this->reportGeneratedAt = $reportGeneratedAt;

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

    public function getCourse(): ?Course
    {
        return $this->course;
    }

    public function setCourse(?Course $course): static
    {
        $this->course = $course;

        return $this;
    }

    public function getGeneratedBy(): ?User
    {
        return $this->generatedBy;
    }

    public function setGeneratedBy(?User $generatedBy): static
    {
        $this->generatedBy = $generatedBy;

        return $this;
    }
}
