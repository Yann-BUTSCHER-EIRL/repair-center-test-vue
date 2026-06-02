<?php

namespace App\Entity;

use App\Entity\Interface\LineInterface;
use App\Repository\LineLabourRepository;
use Doctrine\ORM\Mapping as ORM;
use Override;

#[ORM\Entity(repositoryClass: LineLabourRepository::class)]
class LineLabour implements LineInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $timeSpent = null;

    #[ORM\Column]
    private ?float $hourlyRate = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?LabourType $labourType = null;

    #[ORM\Column]
    private ?float $taxPercentage = null;

    #[ORM\Column]
    private ?float $discountPercentage = null;

    #[ORM\ManyToOne(inversedBy: 'linesLabour')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Quote $quote = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTimeSpent(): ?float
    {
        return $this->timeSpent;
    }

    public function setTimeSpent(float $timeSpent): static
    {
        $this->timeSpent = $timeSpent;

        return $this;
    }

    public function getHourlyRate(): ?float
    {
        return $this->hourlyRate;
    }

    public function setHourlyRate(float $hourlyRate): static
    {
        $this->hourlyRate = $hourlyRate;

        return $this;
    }

    public function getLabourType(): ?LabourType
    {
        return $this->labourType;
    }

    public function setLabourType(?LabourType $labourType): static
    {
        $this->labourType = $labourType;

        return $this;
    }

    public function getTaxPercentage(): ?float
    {
        return $this->taxPercentage;
    }

    public function setTaxPercentage(float $taxPercentage): static
    {
        $this->taxPercentage = $taxPercentage;

        return $this;
    }

    public function getDiscountPercentage(): ?float
    {
        return $this->discountPercentage;
    }

    public function setDiscountPercentage(float $discountPercentage): static
    {
        $this->discountPercentage = $discountPercentage;

        return $this;
    }

    // Line Interface Implementation

    public function getDisplayLabel(): string
    {
        return $this->labourType->getLabel() . ' (' . $this->labourType->getDescription() . ')';
    }

    public function getLineAmountExcludingTaxes(): float
    {
        return $this->hourlyRate;
    }

    public function getLineQuantity(): float
    {
        return $this->timeSpent;
    }

    public function getLineTaxPercentage(): float
    {
        return $this->taxPercentage;
    }

    public function getLineDiscountPercentage(): float
    {
        return $this->discountPercentage;
    }

    public function getQuote(): ?Quote
    {
        return $this->quote;
    }

    public function setQuote(?Quote $quote): static
    {
        $this->quote = $quote;

        return $this;
    }
}
