<?php

namespace Tokirak\Tests\Unit\Domain\Model\ValueObject\CompanyIdentifier;

use PHPUnit\Framework\TestCase;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\CompanyIdentifier\CompanyIdentifier;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\CompanyIdentifier\Siret;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\Locale;

class CompanyIdentifierTest extends TestCase
{
    public function test_create_FR_company_identifier(): void
    {
        $companyIdentifier = CompanyIdentifier::create('12312312312312', Locale::FR_FR);

        self::assertInstanceOf(Siret::class, $companyIdentifier);
        self::assertSame('123 123 123 12312', $companyIdentifier->format());
        self::assertSame('123123123', $companyIdentifier->getSIREN());
        self::assertSame('SIRET', $companyIdentifier->getLabel(Locale::FR_FR));
        self::assertSame('Company ID (SIRET)', $companyIdentifier->getLabel(Locale::EN_GB));
    }

    public function test_create_GP_company_identifier(): void
    {
        $companyIdentifier = CompanyIdentifier::create(
            'AR123456',
            Locale::EN_GB
        );

        self::assertInstanceOf(CompanyIdentifier::class, $companyIdentifier);
        self::assertSame('AR123456', $companyIdentifier->format());
        self::assertSame(
            'Company Number',
            $companyIdentifier->getLabel(Locale::EN_GB)
        );
        self::assertSame(
            'Numéro d\'entreprise (UK)',
            $companyIdentifier->getLabel(Locale::FR_FR)
        );
    }
}
