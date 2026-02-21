<?php

declare(strict_types=1);

namespace Tokirak\InvoiceGenerator\Domain\Model\ValueObject;

enum Currency: string
{
    case EUR = 'EUR';
    case GBP = "GBP";
    case USD = "USD";
    case CHF = "CHF";

    public function getSymbol(): string
    {
        return match ($this) {
            self::EUR => '€',
            self::GBP => '£',
            self::USD => '$',
            self::CHF => 'CHF',
        };
    }

    public function getDecimalPlaces(): int
    {
        return 2;
    }

    public function equals(Currency $currency): bool
    {
        return $this->value == $currency->value;
    }
}
