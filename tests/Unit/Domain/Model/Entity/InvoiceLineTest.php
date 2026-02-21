<?php

namespace Tokirak\Tests\Unit\Domain\Model\Entity;

use PHPUnit\Framework\TestCase;
use Tokirak\InvoiceGenerator\Domain\Model\Entity\InvoiceLine;
use Tokirak\InvoiceGenerator\Domain\Model\Exception\InvalidInvoiceLineException;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\Amount;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\Currency;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\VATRate;

class InvoiceLineTest extends TestCase
{
    public function test_calculate_price_invoice_line(): void
    {
        $invoiceLine = InvoiceLine::create(
            "this is first invoice line",
            2,
            Amount::fromFloat(25, Currency::EUR),
            VATRate::FR_STANDARD
        );

        self::assertInstanceOf(InvoiceLine::class, $invoiceLine);
        self::assertSame(50.0, $invoiceLine->calculateTotalHT()->value);
        self::assertSame(5.0, $invoiceLine->calculateVAT()->value);
        self::assertSame(55.0, $invoiceLine->calculateTotalTTC()->value);
    }

    public function test_throw_exception_invoice_line_on_negative_quantity(): void
    {
        self::expectException(InvalidInvoiceLineException::class);

        InvoiceLine::create(
            "this is first invoice line",
            -2,
            Amount::fromFloat(25, Currency::EUR),
            VATRate::FR_STANDARD
        );
    }
}
