<?php

namespace Tokirak\Tests\Unit\Domain\Model\ValueObject\CompanyIdentifier;

use PHPUnit\Framework\TestCase;
use Tokirak\InvoiceGenerator\Domain\Model\Exception\InvalidCompanyNumberException;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\CompanyIdentifier\CompanyIdentifier;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\CompanyIdentifier\CompanyNumber;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\Locale;

class CompanyNumberTest extends TestCase
{
    public function test_create_company_number(): void
    {
        $companyNumber = CompanyIdentifier::create('AR787778', Locale::EN_GB);

        self::assertInstanceOf(CompanyNumber::class, $companyNumber);
        self::assertSame('AR787778', $companyNumber->format());

        self::assertSame('Company Number', $companyNumber->getLabel(Locale::EN_GB));
        self::assertSame('Numéro d\'entreprise (UK)', $companyNumber->getLabel(Locale::FR_FR));
    }

    public function test_throw_exception_for_invalid_value(): void
    {
        self::expectException(InvalidCompanyNumberException::class);
        CompanyIdentifier::create('AR7877', Locale::EN_GB);
    }
}
