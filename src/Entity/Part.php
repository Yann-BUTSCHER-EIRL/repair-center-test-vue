<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'part')]
class Part
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public $id;

    #[ORM\Column(length: 50, unique: true)]
    public $reference;

    #[ORM\Column(length: 255)]
    public $label;

    #[ORM\Column(type: 'float')]
    public $salePrice;

    /**
     * @var Collection<int, LinePart>
     */
    #[ORM\OneToMany(mappedBy: 'part', targetEntity: LinePart::class)]
    private Collection $lineParts;

    public function __construct()
    {
        $this->lineParts = new ArrayCollection();
    }

    /**
     * @return Collection<int, LinePart>
     */
    public function getLineParts(): Collection
    {
        return $this->lineParts;
    }

    public function addLinePart(LinePart $linePart): static
    {
        if (!$this->lineParts->contains($linePart)) {
            $this->lineParts->add($linePart);
            $linePart->setPart($this);
        }

        return $this;
    }

    public function removeLinePart(LinePart $linePart): static
    {
        if ($this->lineParts->removeElement($linePart)) {
            // set the owning side to null (unless already changed)
            if ($linePart->getPart() === $this) {
                $linePart->setPart(null);
            }
        }

        return $this;
    }
}
