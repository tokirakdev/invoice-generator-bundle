<?php

namespace Tokirak\Tests\Unit\Domain\Model\ValueObject;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Tokirak\InvoiceGenerator\Domain\Model\Exception\InvalidVATNumberException;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\Locale;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\VATNumber;

class VATNumberTest extends TestCase
{
    public function test_can_create_VAT_Number_from_string(): void
    {
        $vatNumber = VATNumber::fromString('FR56543456767');

        self::assertInstanceOf(VATNumber::class, $vatNumber);
        self::assertSame('FR56543456767', $vatNumber->value);
        self::assertSame('FR 56 543456767', $vatNumber->format());
        self::assertSame('FR', $vatNumber->countryCode);
        self::assertSame('N° TVA intracommunautaire', $vatNumber->getLabel(
            Locale::FR_FR
        ));
    }

    #[DataProvider('provideWrongVatFormat')]
    public function test_throw_exception_on_creating_VAT_Number_from_string(
        string $value,
        string $expectedErrorMessage
    ): void {
        self::expectException(InvalidVATNumberException::class);
        self::expectExceptionMessage($expectedErrorMessage);
        VATNumber::fromString($value);
    }

    public static function provideWrongVatFormat(): \Generator
    {
        yield 'too short chars length' => ['value' => '1',
            'expectedErrorMessage' => InvalidVATNumberException::Length()];
        yield 'Invalid VAT number format' => ['value' => 'A232332',
            'expectedErrorMessage' => InvalidVATNumberException::invalidFormat()];
        yield 'Invalid FR VAT number format' => ['value' => 'FR5656',
            'expectedErrorMessage' => InvalidVATNumberException::invalidFRFormat()];
        yield 'Invalid GP VAT number format' => ['value' => 'GP56543456789',
            'expectedErrorMessage' => InvalidVATNumberException::invalidGPFormat()];
    }
}
