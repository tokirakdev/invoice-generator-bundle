<?php

namespace Tokirak\Tests\Unit\Domain\Model\Entity;

use PHPUnit\Framework\TestCase;
use Tokirak\InvoiceGenerator\Domain\Model\Entity\Customer;
use Tokirak\InvoiceGenerator\Domain\Model\Entity\Invoice;
use Tokirak\InvoiceGenerator\Domain\Model\Entity\InvoiceLine;
use Tokirak\InvoiceGenerator\Domain\Model\Entity\Supplier;
use Tokirak\InvoiceGenerator\Domain\Model\Exception\InvalidInvoiceException;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\Address;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\Amount;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\CompanyIdentifier\Siret;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\Currency;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\InvoiceNumber;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\Locale;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\VATNumber;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\VATRate;

class InvoiceTest extends TestCase
{
    private Locale $locale = Locale::FR_FR;
    private Currency $currency = Currency::EUR;
    private Supplier $supplier;
    private Customer $customer;
    private Invoice $invoice;

    protected function setUp(): void
    {
        $this->supplier = new Supplier(
            name: 'Ma Société SARL',
            address: new Address(
                street: '123 Rue de la Paix',
                zipCode: '75001',
                city: 'Paris'
            ),
            companyId: new Siret('12345678901234'),
            VATNumber: VATNumber::fromString('FR12345678901')
        );

        $this->customer = new Customer(
            name: 'Client SAS',
            address: new Address(
                street: '456 Avenue des Champs',
                zipCode: '69001',
                city: 'Lyon'
            )
        );

        $this->invoice = Invoice::create(
            InvoiceNumber::fromString('gen-333'),
            $this->supplier,
            $this->customer,
            new \DateTimeImmutable(),
            new \DateTimeImmutable()
        );
    }


    public function test_create_an_invoice(): void
    {
        $issuedAt = new \DateTimeImmutable();
        $dueAt = new \DateTimeImmutable();
        $invoice = Invoice::create(
            InvoiceNumber::fromString('gen-333'),
            $this->supplier,
            $this->customer,
            $issuedAt,
            $dueAt
        );

        self::assertInstanceOf(Invoice::class, $invoice);
        self::assertSame(
            $issuedAt->format(\DateTimeInterface::ATOM),
            $invoice->issuedAt->format(\DateTimeInterface::ATOM)
        );
        self::assertSame(
            $dueAt->format(\DateTimeInterface::ATOM),
            $invoice->dueAt->format(\DateTimeInterface::ATOM)
        );
    }

    public function test_add_invoice_line(): void
    {
        $invoiceLine = InvoiceLine::create(
            "description",
            1,
            Amount::fromFloat(30, $this->currency),
            VATRate::FR_STANDARD
        );
        $invoiceLine2 = InvoiceLine::create(
            "description 2",
            1,
            Amount::fromFloat(10, $this->currency),
            VATRate::FR_STANDARD
        );

        $this->invoice->addLine($invoiceLine)->addLine($invoiceLine2);

        self::assertCount(2, $this->invoice->getLines());
        self::assertSame(48.0, $this->invoice->calculateTotalTTC()->value);
        self::assertSame(40.0, $this->invoice->calculateTotalHT()->value);
        self::assertSame(8.0, $this->invoice->calculateTotalVAT()->value);
    }

    public function test_throw_exception_when_adding_line_with_different_currency(): void
    {
        self::expectException(InvalidInvoiceException::class);
        self::expectExceptionMessage('Line currency must match invoice currency: EUR');
        $invoiceLine = InvoiceLine::create(
            "description",
            1,
            Amount::fromFloat(30, Currency::GBP),
            VATRate::FR_STANDARD
        );
        $this->invoice->addLine($invoiceLine);
    }

    public function test_tva_breakdown(): void
    {
        $invoiceLine = InvoiceLine::create(
            "description",
            1,
            Amount::fromFloat(30, $this->currency),
            VATRate::FR_STANDARD
        );
        $invoiceLine2 = InvoiceLine::create(
            "description",
            1,
            Amount::fromFloat(30, $this->currency),
            VATRate::FR_STANDARD
        );
        $invoiceLine3 = InvoiceLine::create(
            "description",
            1,
            Amount::fromFloat(30, $this->currency),
            VATRate::FR_REDUCED
        );
        $invoiceLine4 = InvoiceLine::create(
            "description",
            1,
            Amount::fromFloat(30, $this->currency),
            VATRate::FR_SUPER_REDUCED
        );
        $this->invoice
            ->addLine($invoiceLine)
            ->addLine($invoiceLine2)
            ->addLine($invoiceLine3)
            ->addLine($invoiceLine4);

        $vatBreakdown = $this->invoice->getVATBreakdown();

        self::assertCount(3, $vatBreakdown);
        self::assertArrayHasKey("20", $vatBreakdown);
        self::assertArrayHasKey("10", $vatBreakdown);
        self::assertArrayHasKey("5", $vatBreakdown);

        self::assertSame(12.0, $vatBreakdown["20"]['vat']->value);
        self::assertSame(3.0, $vatBreakdown["10"]['vat']->value);
        self::assertSame(1.5, $vatBreakdown["5"]['vat']->value);
    }
}
