<?php

declare(strict_types=1);

namespace Tokirak\InvoiceGenerator\Domain\Model\ValueObject\CompanyIdentifier;

use Tokirak\InvoiceGenerator\Domain\Model\Exception\UnsupportedLocaleException;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\Locale;

abstract class CompanyIdentifier
{
    public function __construct(
        public string $value
    ) {
        $this->validate();
    }
    abstract protected function validate(): void;
    abstract public function format(): string;
    abstract public function getLabel(Locale $locale): string;

    public static function create(string $value, Locale $locale): self
    {
        return match ($locale) {
            Locale::FR_FR => new Siret($value),
            Locale::EN_GB => new CompanyNumber($value),
            default => throw new UnsupportedLocaleException($locale->value)
        };
    }
}
