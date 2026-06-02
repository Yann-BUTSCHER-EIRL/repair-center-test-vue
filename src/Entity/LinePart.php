<?php

namespace App\Entity;

use App\Entity\Interface\LineInterface;
use App\Repository\LinePartRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LinePartRepository::class)]
class LinePart implements LineInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;   

    #[ORM\ManyToOne(inversedBy: 'lineParts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Part $part = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column]
    private ?float $taxPercentage = null;

    #[ORM\Column]
    private ?float $discountPercentage = null;

    #[ORM\ManyToOne(inversedBy: 'linesPart')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Quote $quote = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPart(): ?Part
    {
        return $this->part;
    }

    public function setPart(?Part $part): static
    {
        $this->part = $part;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

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
        return $this->part->reference . ' - ' . $this->part->label;
    }

    public function getLineAmountExcludingTaxes(): float
    {
        return $this->part->salePrice;
    }

    public function getLineQuantity(): float
    {
        return floatval($this->quantity);
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
