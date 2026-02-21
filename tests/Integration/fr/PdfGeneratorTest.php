<?php

namespace Tokirak\Tests\Integration\fr;

use Smalot\PdfParser\Parser;
use Tokirak\InvoiceGenerator\Domain\Model\Entity\Customer;
use Tokirak\InvoiceGenerator\Domain\Model\Entity\Invoice;
use Tokirak\InvoiceGenerator\Domain\Model\Entity\InvoiceLine;
use Tokirak\InvoiceGenerator\Domain\Model\Entity\Supplier;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\Address;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\Amount;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\CompanyIdentifier\Siret;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\Currency;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\InvoiceNumber;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\VATNumber;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\VATRate;
use Tokirak\InvoiceGenerator\Infrastructure\Generator\PdfGenerator;
use Tokirak\Tests\Integration\IntegrationTestCase;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class PdfGeneratorTest extends IntegrationTestCase
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
            companyId: new Siret('12345678901234'),
            VATNumber: VATNumber::fromString('FR12345678901')
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
            new \DateTimeImmutable()
        );

        $invoiceLine = InvoiceLine::create(
            "first article here",
            2,
            Amount::fromFloat(12.0, Currency::EUR),
            VATRate::FR_STANDARD
        );
        $anotherInvoiceLine = InvoiceLine::create(
            "second article here",
            2,
            Amount::fromFloat(78.55, Currency::EUR),
            VATRate::FR_REDUCED
        );

        $invoice->addLine($invoiceLine)->addLine($anotherInvoiceLine);

        $generator = new PdfGenerator($this->twig, []);

        $expectedPdf = $generator->generate($invoice);

        $parser = new Parser();
        $pdfContent = $parser->parseContent($expectedPdf);
        $textContent = $pdfContent->getText();

        self::assertStringStartsWith('%PDF-', $expectedPdf);
        self::assertStringContainsString('FACTURE N° test-from-string', $textContent);
        self::assertStringContainsString('Ma Société SARL', $textContent);
        self::assertStringContainsString('SIRET: 123 456 789 01234', $textContent);
        self::assertStringContainsString('Client SAS', $textContent);
        self::assertStringContainsString('first article here', $textContent);
        self::assertStringContainsString('second article here', $textContent);
        self::assertStringContainsString('Total HT:', $textContent);
        self::assertStringContainsString('TVA 20%:', $textContent);
        self::assertStringContainsString('TVA 10%:', $textContent);
    }
}
