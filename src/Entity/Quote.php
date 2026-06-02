<?php

namespace App\Entity;

use App\Repository\QuoteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuoteRepository::class)]
class Quote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $reference = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'quotes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?RepairOrder $repairOrder = null;

    /**
     * @var Collection<int, LinePart>
     */
    #[ORM\OneToMany(mappedBy: 'quote', targetEntity: LinePart::class, orphanRemoval: true)]
    private Collection $linesPart;

    /**
     * @var Collection<int, LineLabour>
     */
    #[ORM\OneToMany(mappedBy: 'quote', targetEntity: LineLabour::class, orphanRemoval: true)]
    private Collection $linesLabour;

    public function __construct()
    {
        $this->linesPart = new ArrayCollection();
        $this->linesLabour = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getRepairOrder(): ?RepairOrder
    {
        return $this->repairOrder;
    }

    public function setRepairOrder(?RepairOrder $repairOrder): static
    {
        $this->repairOrder = $repairOrder;

        return $this;
    }

    /**
     * @return Collection<int, LinePart>
     */
    public function getLinesPart(): Collection
    {
        return $this->linesPart;
    }

    public function addLinesPart(LinePart $linesPart): static
    {
        if (!$this->linesPart->contains($linesPart)) {
            $this->linesPart->add($linesPart);
            $linesPart->setQuote($this);
        }

        return $this;
    }

    public function removeLinesPart(LinePart $linesPart): static
    {
        if ($this->linesPart->removeElement($linesPart)) {
            // set the owning side to null (unless already changed)
            if ($linesPart->getQuote() === $this) {
                $linesPart->setQuote(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, LineLabour>
     */
    public function getLinesLabour(): Collection
    {
        return $this->linesLabour;
    }

    public function addLinesLabour(LineLabour $linesLabour): static
    {
        if (!$this->linesLabour->contains($linesLabour)) {
            $this->linesLabour->add($linesLabour);
            $linesLabour->setQuote($this);
        }

        return $this;
    }

    public function removeLinesLabour(LineLabour $linesLabour): static
    {
        if ($this->linesLabour->removeElement($linesLabour)) {
            // set the owning side to null (unless already changed)
            if ($linesLabour->getQuote() === $this) {
                $linesLabour->setQuote(null);
            }
        }

        return $this;
    }
}
