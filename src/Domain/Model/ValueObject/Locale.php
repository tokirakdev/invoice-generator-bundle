<?php

declare(strict_types=1);

namespace Tokirak\InvoiceGenerator\Domain\Model\ValueObject;

enum Locale: string
{
    case FR_FR = "fr_FR";
    case EN_GB = 'en_GB';
    case DE_DE = 'de_DE';
    case ES_ES = 'es_ES';

    public function getCountryCode(): string
    {
        return substr($this->value, 3, 2); // FR, GB ...
    }
    public function getLanguageCode(): string
    {
        return substr($this->value, 0, 2); // fr, en ...
    }

    public function getCurrency(): Currency
    {
        return match ($this) {
            self::FR_FR, self::DE_DE, self::ES_ES => Currency::EUR,
            self::EN_GB => Currency::GBP
        };
    }
}
