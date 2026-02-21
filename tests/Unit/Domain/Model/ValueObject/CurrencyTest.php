<?php

namespace Tokirak\Tests\Unit\Domain\Model\ValueObject;

use PHPUnit\Framework\TestCase;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\Currency;

class CurrencyTest extends TestCase
{
    public function test_currency(): void
    {
        $currency = Currency::CHF;

        self::assertSame('CHF', $currency->getSymbol());
        self::assertSame(2, $currency->getDecimalPlaces());
    }
}
