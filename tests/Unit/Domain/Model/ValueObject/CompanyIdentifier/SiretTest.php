<?php

namespace Tokirak\Tests\Unit\Domain\Model\ValueObject\CompanyIdentifier;

use PHPUnit\Framework\TestCase;
use Tokirak\InvoiceGenerator\Domain\Model\Exception\InvalidSiretException;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\CompanyIdentifier\CompanyIdentifier;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\CompanyIdentifier\Siret;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\Locale;

class SiretTest extends TestCase
{
    public function test_create_siret(): void
    {
        $siret  = CompanyIdentifier::create('87788877778877', Locale::FR_FR);

        self::assertInstanceOf(Siret::class, $siret);
        self::assertSame('SIRET', $siret->getLabel(Locale::FR_FR));
        self::assertSame('Company ID (SIRET)', $siret->getLabel(Locale::EN_GB));
        self::assertSame('877 888 777 78877', $siret->format());
        self::assertSame('877888777', $siret->getSIREN());
    }

    public function test_throw_exception_on_invalid_siret_value(): void
    {
        self::expectException(InvalidSiretException::class);
        self::expectExceptionMessage(InvalidSiretException::LENGTH);
        CompanyIdentifier::create('3444', Locale::FR_FR);
    }
}
