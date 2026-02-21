<?php

namespace Tokirak\Tests\Unit\Domain\Model\ValueObject;

use PHPUnit\Framework\TestCase;
use Tokirak\InvoiceGenerator\Domain\Model\Exception\InvalidAmountException;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\Amount;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\Currency;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\Locale;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\VATRate;

use function PHPUnit\Framework\assertSame;

class AmountTest extends TestCase
{
    public function test_create_amount_from_float(): void
    {
        $amount = Amount::fromFloat(23.34, Currency::EUR);

        self:assertSame(23.34, $amount->value);
        self::assertSame("EUR", $amount->currency->value);
    }

    public function test_create_zero_amount(): void
    {
        $amount = Amount::zero(Currency::GBP);

        self:assertSame(0.0, $amount->value);
        self::assertSame("GBP", $amount->currency->value);
    }

    public function test_throw_exception_on_negative_amount(): void
    {
        self::expectException(InvalidAmountException::class);
        self::expectExceptionMessage(InvalidAmountException::NOT_NEGATIVE);

        Amount::fromFloat(-34, Currency::CHF);
    }

    public function test_add_operation_amount(): void
    {
        $amount = Amount::fromFloat(23, Currency::EUR);
        $anotherAmount = Amount::fromFloat(2, Currency::EUR);

        $addedAmount = $amount->add($anotherAmount);

        self::assertNotSame($amount, $addedAmount);
        self::assertSame(25.0, $addedAmount->value);
        self::assertSame('EUR', $addedAmount->currency->value);
    }

    public function test_multiply_operation_amount(): void
    {
        $amount = Amount::fromFloat(23, Currency::EUR);
        $newAmount = $amount->multiply(4);

        self::assertNotSame($amount, $newAmount);
        self::assertSame(92.0, $newAmount->value);
        self::assertSame('EUR', $newAmount->currency->value);
    }

    public function test_apply_vat(): void
    {
        $amount = Amount::fromFloat(20, Currency::EUR);

        $newAmount = $amount->applyVAT(VATRate::FR_STANDARD);

        self::assertNotSame($amount, $newAmount);
        self::assertSame(24.0, $newAmount->value);
    }

    public function test_format_amount(): void
    {
        $amount = Amount::fromFloat(20, Currency::EUR);

        self::assertSame("20,00\u{A0}€", $amount->format(Locale::FR_FR));
    }

    public function test_throw_exception_on_amount_operation_with_different_currency(): void
    {
        self::expectException(InvalidAmountException::class);
        $amount = Amount::fromFloat(20, Currency::EUR);
        $amount->add(Amount::zero(Currency::CHF));
    }
}
