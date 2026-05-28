<?php

namespace App\Entity;

use App\Repository\ReportRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReportRepository::class)]
class Report
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $reportType = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $reportData = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $reportGeneratedAt = null;

    #[ORM\ManyToOne(inversedBy: 'reports')]
    private ?Classe $classe = null;

    #[ORM\ManyToOne(inversedBy: 'reports')]
    private ?Course $course = null;

    #[ORM\ManyToOne(inversedBy: 'reports')]
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
