<?php

namespace Tokirak\Tests\Unit\Domain\Model\ValueObject;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\Locale;

class LocaleTest extends TestCase
{
    #[DataProvider('provideLocaleData')]
    public function test_locale(
        Locale $locale,
        string $countryCode,
        string $languageCode,
        string $currency
    ): void {
        self::assertSame($countryCode, $locale->getCountryCode());
        self::assertSame($languageCode, $locale->getLanguageCode());
        self::assertSame($currency, $locale->getCurrency()->value);
    }

    public static function provideLocaleData(): \Generator
    {
        yield "FR" => ["locale" => Locale::FR_FR,
            "countryCode" => "FR", "languageCode" => "fr", "currency" => 'EUR'];
        yield "GB" => ["locale" => Locale::EN_GB,
            "countryCode" => "GB", "languageCode" => "en", "currency" => 'GBP'];
        yield "DE" => ["locale" => Locale::DE_DE,
            "countryCode" => "DE", "languageCode" => "de", "currency" => 'EUR'];
    }
}
