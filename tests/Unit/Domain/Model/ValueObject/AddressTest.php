<?php

namespace Tokirak\Tests\Unit\Domain\Model\ValueObject;

use PHPUnit\Framework\TestCase;
use Tokirak\InvoiceGenerator\Domain\Model\Exception\InvalidAddressException;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\Address;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\Locale;

class AddressTest extends TestCase
{
    public function test_create_address(): void
    {
        $address = new Address(
            '12 rue de la marniere',
            '95100',
            'cergy',
            'France',
            'France'
        );

        $expectedFR = <<<TEXT
            12 rue de la marniere
            95100 cergy
            TEXT;


        self::assertInstanceOf(Address::class, $address);
        self::assertSame(
            '12 rue de la marniere' . PHP_EOL . '95100 cergy',
            $address->format()
        );
        self::assertSame(
            '12 rue de la marniere' . PHP_EOL . 'cergy' . PHP_EOL . '95100',
            $address->format(Locale::EN_GB)
        );
        self::assertSame(
            '12 rue de la marniere' . PHP_EOL . '95100 cergy'
            . PHP_EOL . 'France' . PHP_EOL . 'France',
            $address->format(Locale::ES_ES)
        );
    }

    public function test_throw_exception_for_empty_address_value(): void
    {
        self::expectException(InvalidAddressException::class);
        self::expectExceptionMessage('Street, zipCode and city are required');

        new Address('', '', '');
    }
}
