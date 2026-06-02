<?php

namespace App\Service;

use App\Entity\Interface\LineInterface;

class QuoteService
{
    public function calculateLinePriceExcludingTaxes(LineInterface $line)
    {
        $priceExcludingTaxes = $line->getLineAmountExcludingTaxes() * $line->getLineQuantity();

        return $priceExcludingTaxes - ($priceExcludingTaxes * $line->getLineDiscountPercentage()) / 100;
    }

    public function calculateLinePriceWithTaxes(LineInterface $line)
    {
        $priceExcludingTaxes = $this->calculateLinePriceExcludingTaxes($line);

        return $priceExcludingTaxes + ($priceExcludingTaxes * $line->getLineTaxPercentage()) / 100;
    }
   
    public function calculateTotalPriceExcludingTaxes(LineInterface ...$lines)
    {
        $totalPriceExcludingTaxes = 0;

        foreach($lines as $line) {
            $totalPriceExcludingTaxes += $this->calculateLinePriceExcludingTaxes($line);
        }

        return $totalPriceExcludingTaxes;
    }

    public function calculateTotalPriceWithTaxes(LineInterface ...$lines)
    {
        $totalPriceWithTaxes = 0;

        foreach($lines as $line) {
            $totalPriceWithTaxes += $this->calculateLinePriceWithTaxes($line);
        }

        return $totalPriceWithTaxes;
    }
}