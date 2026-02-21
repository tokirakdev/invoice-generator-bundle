<?php

declare(strict_types=1);

namespace Tokirak\InvoiceGenerator\Domain\Model\ValueObject;

enum VATRate
{
    // France
    case FR_STANDARD;
    case FR_REDUCED;
    case FR_SUPER_REDUCED;

    // UK
    case UK_STANDARD;
    case UK_REDUCED;

    // Allemagne
    case DE_STANDARD;
    case DE_REDUCED;

    // Espagne
    case ES_STANDARD;
    case ES_REDUCED;
    case ES_SUPER_REDUCED;

    public function value(): float
    {
        return match ($this) {
            self::FR_STANDARD, self::UK_STANDARD => 0.20,
            self::FR_REDUCED, self::ES_REDUCED => 0.10,
            self::UK_REDUCED, self::FR_SUPER_REDUCED => 0.05,
            self::DE_STANDARD => 0.19,
            self::DE_REDUCED => 0.07,
            self::ES_STANDARD => 0.21,
            self::ES_SUPER_REDUCED => 0.04,
            default => 0.0
        };
    }

    public function format(): string
    {
        return $this->toPercentage() . '%';
    }
    public function toPercentage(): float
    {
        return $this->value() * 100;
    }
}
