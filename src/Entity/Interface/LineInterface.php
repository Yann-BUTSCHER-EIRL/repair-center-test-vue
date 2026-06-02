<?php

namespace App\Entity\Interface;

interface LineInterface
{
    public function getDisplayLabel(): string;

    public function getLineAmountExcludingTaxes(): float;

    public function getLineQuantity(): float;

    public function getLineTaxPercentage(): float;

    public function getLineDiscountPercentage(): float;
}