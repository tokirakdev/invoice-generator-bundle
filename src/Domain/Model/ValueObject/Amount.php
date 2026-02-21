<?php

declare(strict_types=1);

namespace Tokirak\InvoiceGenerator\Domain\Model\ValueObject;

use Tokirak\InvoiceGenerator\Domain\Model\Exception\InvalidAmountException;

readonly class Amount
{
    private function __construct(
        public float $value,
        public Currency $currency
    ) {
        if ($this->value < 0) {
            InvalidAmountException::throwNonNegativeAmount();
        }
    }

    public static function fromFloat(float $value, Currency $currency): self
    {
        return new self($value, $currency);
    }
    public static function zero(Currency $currency): self
    {
        return new self(0, $currency);
    }

    public function add(Amount $amount): self
    {
        $this->ensureSameCurrency($amount);
        return new self($this->value + $amount->value, $this->currency);
    }

    public function multiply(float $factor): self
    {
        return new self($this->value * $factor, $this->currency);
    }

    public function applyVAT(VATRate $VATRate): self
    {
        return $this->multiply(1 + $VATRate->value());
    }

    public function format(Locale $locale): string
    {
        $formatter = new \NumberFormatter($locale->value, \NumberFormatter::CURRENCY);
        return $formatter->formatCurrency($this->value, $this->currency->value);
    }

    private function ensureSameCurrency(Amount $amount): void
    {
        if (!$this->currency->equals($amount->currency)) {
            InvalidAmountException::throwDifferentCurrencyOperation(
                $this->currency->value,
                $amount->currency->value
            );
        }
    }
}
