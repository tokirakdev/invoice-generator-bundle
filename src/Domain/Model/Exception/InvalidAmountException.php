<?php

namespace Tokirak\InvoiceGenerator\Domain\Model\Exception;

class InvalidAmountException extends \DomainException
{
    public const string NOT_NEGATIVE = "Amount cannot be negative";
    public const string DIFFERENT_CURRENCY = "Cannot operate on different currencies: %s vs %s";

    public static function throwNonNegativeAmount(): void
    {
        throw new self(self::NOT_NEGATIVE);
    }

    public static function throwDifferentCurrencyOperation(
        string $currency,
        string $anotherCurrency
    ): void {
        throw new self(sprintf(self::DIFFERENT_CURRENCY, $currency, $anotherCurrency));
    }
}
