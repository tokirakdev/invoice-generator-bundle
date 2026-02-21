<?php

namespace Tokirak\Tests\Unit\Domain\Model\ValueObject;

use PHPUnit\Framework\TestCase;
use Tokirak\InvoiceGenerator\Domain\Model\Exception\InvalidInvoiceNumberException;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\InvoiceNumber;

class InvoiceNumberTest extends TestCase
{
    public function test_create_invoice_number_from_string(): void
    {
        $invoiceNumber = InvoiceNumber::fromString('test');
        self::assertSame('test', $invoiceNumber->format());
    }

    public function test_generate_invoice_number(): void
    {
        $invoiceNumber = InvoiceNumber::generate('DUA', 2025, 201);
        self::assertSame('DUA-2025-0201', $invoiceNumber->format());
    }

    public function test_throw_exception_on_invalid_value(): void
    {
        self::expectException(InvalidInvoiceNumberException::class);
        self::expectExceptionMessage('Invoice number cannot be empty');

        InvoiceNumber::fromString('');
    }
}
