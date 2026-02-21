<?php

namespace Tokirak\Tests\Unit\Domain\Model\ValueObject;

use PHPUnit\Framework\TestCase;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\VATRate;

class VATRateTest extends TestCase
{
    public function test_get_vat_value(): void
    {
        $vatRateFR = VATRate::FR_STANDARD;

        self::assertSame(0.2, $vatRateFR->value());
        self::assertSame(20.0, $vatRateFR->toPercentage());
        self::assertSame("20%", $vatRateFR->format());
    }
}
