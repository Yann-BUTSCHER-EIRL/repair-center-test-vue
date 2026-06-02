<?php

namespace App\Entity;

use App\Repository\LabourTypeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LabourTypeRepository::class)]
class LabourType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $indicativeHourlyRate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getIndicativeHourlyRate(): ?float
    {
        return $this->indicativeHourlyRate;
    }

    public function setIndicativeHourlyRate(float $indicativeHourlyRate): static
    {
        $this->indicativeHourlyRate = $indicativeHourlyRate;

        return $this;
    }
}
