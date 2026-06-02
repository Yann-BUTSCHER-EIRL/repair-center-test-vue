<?php

namespace App\Tests\Service;

use App\Entity\Interface\LineInterface;
use App\Service\QuoteService;
use PHPUnit\Framework\TestCase;

class QuoteServiceTest extends TestCase
{
    private QuoteService $quoteService;

    protected function setUp(): void
    {
        $this->quoteService = new QuoteService();
    }

    public function testCalculateLinePriceExcludingTaxesWithoutDiscount(): void
    {
        $line = $this->createLineMock(
            amountExcludingTaxes: 100,
            quantity: 2,
            taxPercentage: 20,
            discountPercentage: 0
        );

        $result = $this->quoteService->calculateLinePriceExcludingTaxes($line);

        $this->assertEquals(200, $result);
    }

    public function testCalculateLinePriceExcludingTaxesWithDiscount(): void
    {
        $line = $this->createLineMock(
            amountExcludingTaxes: 100,
            quantity: 2,
            taxPercentage: 20,
            discountPercentage: 10
        );

        $result = $this->quoteService->calculateLinePriceExcludingTaxes($line);

        // 100 * 2 = 200
        // -10% = 180
        $this->assertEquals(180, $result);
    }

    public function testCalculateLinePriceWithTaxesWithoutDiscount(): void
    {
        $line = $this->createLineMock(
            amountExcludingTaxes: 100,
            quantity: 2,
            taxPercentage: 20,
            discountPercentage: 0
        );

        $result = $this->quoteService->calculateLinePriceWithTaxes($line);

        // 200 + 20%
        $this->assertEquals(240, $result);
    }

    public function testCalculateLinePriceWithTaxesWithDiscount(): void
    {
        $line = $this->createLineMock(
            amountExcludingTaxes: 100,
            quantity: 2,
            taxPercentage: 20,
            discountPercentage: 10
        );

        $result = $this->quoteService->calculateLinePriceWithTaxes($line);

        // HT = 180
        // TTC = 216
        $this->assertEquals(216, $result);
    }

    public function testCalculateTotalPriceExcludingTaxes(): void
    {
        $line1 = $this->createLineMock(
            amountExcludingTaxes: 100,
            quantity: 2,
            taxPercentage: 20,
            discountPercentage: 10
        );

        $line2 = $this->createLineMock(
            amountExcludingTaxes: 50,
            quantity: 3,
            taxPercentage: 20,
            discountPercentage: 0
        );

        $result = $this->quoteService->calculateTotalPriceExcludingTaxes(
            $line1,
            $line2
        );

        // 180 + 150
        $this->assertEquals(330, $result);
    }

    public function testCalculateTotalPriceWithTaxes(): void
    {
        $line1 = $this->createLineMock(
            amountExcludingTaxes: 100,
            quantity: 2,
            taxPercentage: 20,
            discountPercentage: 10
        );

        $line2 = $this->createLineMock(
            amountExcludingTaxes: 50,
            quantity: 3,
            taxPercentage: 20,
            discountPercentage: 0
        );

        $result = $this->quoteService->calculateTotalPriceWithTaxes(
            $line1,
            $line2
        );

        // 216 + 180
        $this->assertEquals(396, $result);
    }

    public function testCalculateTotalPriceExcludingTaxesWithoutLines(): void
    {
        $result = $this->quoteService->calculateTotalPriceExcludingTaxes();

        $this->assertEquals(0, $result);
    }

    public function testCalculateTotalPriceWithTaxesWithoutLines(): void
    {
        $result = $this->quoteService->calculateTotalPriceWithTaxes();

        $this->assertEquals(0, $result);
    }

    private function createLineMock(
        float $amountExcludingTaxes,
        float $quantity,
        float $taxPercentage,
        float $discountPercentage
    ): LineInterface {
        $line = $this->createMock(LineInterface::class);

        $line
            ->method('getLineAmountExcludingTaxes')
            ->willReturn($amountExcludingTaxes);

        $line
            ->method('getLineQuantity')
            ->willReturn($quantity);

        $line
            ->method('getLineTaxPercentage')
            ->willReturn($taxPercentage);

        $line
            ->method('getLineDiscountPercentage')
            ->willReturn($discountPercentage);

        return $line;
    }
}