<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'repair_order')]
class RepairOrder
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public $id;

    #[ORM\Column(length: 50, unique: true)]
    public $reference;

    // Statuts possibles : PENDING, IN_PROGRESS, WAITING_PARTS, DONE, DELIVERED, CANCELLED
    #[ORM\Column(length: 50)]
    public $status = 'PENDING';

    #[ORM\ManyToOne(targetEntity: Customer::class)]
    #[ORM\JoinColumn(nullable: true)]
    public $customer;

    #[ORM\Column(type: 'float')]
    public $totalAmount = 0;

    #[ORM\Column(type: 'datetime')]
    public $createdAt;

    #[ORM\Column(length: 1000, nullable: true)]
    public $description;

    /**
     * @var Collection<int, Quote>
     */
    #[ORM\OneToMany(mappedBy: 'repairOrder', targetEntity: Quote::class)]
    private Collection $quotes;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->quotes = new ArrayCollection();
    }

    /**
     * @return Collection<int, Quote>
     */
    public function getQuotes(): Collection
    {
        return $this->quotes;
    }

    public function addQuote(Quote $quote): static
    {
        if (!$this->quotes->contains($quote)) {
            $this->quotes->add($quote);
            $quote->setRepairOrder($this);
        }

        return $this;
    }

    public function removeQuote(Quote $quote): static
    {
        if ($this->quotes->removeElement($quote)) {
            // set the owning side to null (unless already changed)
            if ($quote->getRepairOrder() === $this) {
                $quote->setRepairOrder(null);
            }
        }

        return $this;
    }
}
