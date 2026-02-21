<?php

namespace Tokirak\Tests\Integration\en;

use Smalot\PdfParser\Parser;
use Tokirak\InvoiceGenerator\Domain\Model\Entity\Customer;
use Tokirak\InvoiceGenerator\Domain\Model\Entity\Invoice;
use Tokirak\InvoiceGenerator\Domain\Model\Entity\InvoiceLine;
use Tokirak\InvoiceGenerator\Domain\Model\Entity\Supplier;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\Address;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\Amount;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\CompanyIdentifier\CompanyNumber;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\Currency;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\InvoiceNumber;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\Locale;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\VATNumber;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\VATRate;
use Tokirak\InvoiceGenerator\Infrastructure\Generator\PdfGenerator;
use Tokirak\Tests\Integration\ENIntegrationTestCase;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class PdfGeneratorENTest extends ENIntegrationTestCase
{
    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     * @throws \Exception
     */
    public function test_generate_invoice_pdf(): void
    {
        $supplier = new Supplier(
            name: 'Ma Société SARL',
            address: new Address(
                street: '123 Rue de la Paix',
                zipCode: '75001',
                city: 'Paris'
            ),
            companyId: new CompanyNumber('AR123456'),
            VATNumber: VATNumber::fromString('UK12345678901')
        );

        $customer = new Customer(
            name: 'Client SAS',
            address: new Address(
                street: '456 Avenue des Champs',
                zipCode: '69001',
                city: 'Lyon'
            )
        );

        $invoice = Invoice::create(
            InvoiceNumber::fromString('test-from-string'),
            $supplier,
            $customer,
            new \DateTimeImmutable(),
            new \DateTimeImmutable(),
            Currency::GBP
        );

        $invoiceLine = InvoiceLine::create(
            "first article here",
            2,
            Amount::fromFloat(12.0, Currency::GBP),
            VATRate::UK_STANDARD
        );
        $anotherInvoiceLine = InvoiceLine::create(
            "second article here",
            2,
            Amount::fromFloat(78.55, Currency::GBP),
            VATRate::UK_STANDARD
        );

        $invoice->addLine($invoiceLine)->addLine($anotherInvoiceLine);

        $generator = new PdfGenerator($this->twig, []);

        $expectedPdf = $generator->generate($invoice, Locale::EN_GB);

        $parser = new Parser();
        $pdfContent = $parser->parseContent($expectedPdf);
        $textContent = $pdfContent->getText();

        self::assertStringStartsWith('%PDF-', $expectedPdf);
        self::assertStringContainsString('INVOICE N° test-from-string', $textContent);
        self::assertStringContainsString('Ma Société SARL', $textContent);
        self::assertStringContainsString('Company Number: AR123456', $textContent);
        self::assertStringContainsString('Client SAS', $textContent);
        self::assertStringContainsString('first article here', $textContent);
        self::assertStringContainsString('second article here', $textContent);
        self::assertStringContainsString('Total (excl. VAT):', $textContent);
        self::assertStringContainsString('Total (incl. VAT):', $textContent);
        self::assertStringContainsString('VAT 20%:', $textContent);
        self::assertStringContainsString('£78.55', $textContent);
        self::assertStringContainsString('£12.0', $textContent);
    }
}
