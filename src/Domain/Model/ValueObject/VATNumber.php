<?php

declare(strict_types=1);

namespace Tokirak\InvoiceGenerator\Domain\Model\ValueObject;

use Tokirak\InvoiceGenerator\Domain\Model\Exception\InvalidVATNumberException;

readonly class VATNumber
{
    public function __construct(
        public string $value,
        public string $countryCode
    ) {
        $this->validate();
    }

    public static function fromString(string $value): self
    {
        $value = strtoupper(str_replace(' ', '', $value));

        if (strlen($value) < 2) {
            throw new InvalidVATNumberException(
                InvalidVATNumberException::Length()
            );
        }

        $countryCode = substr($value, 0, 2);

        return new self($value, $countryCode);
    }

    public function format(): string
    {
        return match ($this->countryCode) {
            'FR' => $this->countryCode . ' '
                . substr($this->value, 2, 2) . ' '
                . substr($this->value, 4),
            'GB' => $this->countryCode . ' ' . substr($this->value, 2),
            default => $this->value
        };
    }

    public function getLabel(Locale $locale): string
    {
        return match ($locale) {
            Locale::FR_FR => 'N° TVA intracommunautaire',
            Locale::EN_GB => 'VAT Number',
            Locale::DE_DE => 'USt-IdNr.',
            Locale::ES_ES => 'NIF-IVA',
            default => 'VAT Number'
        };
    }

    private function validate(): void
    {
        // general format EU
        if (!preg_match('/^[A-Z]{2}[A-Z0-9]{2,13}$/', $this->value)) {
            throw new InvalidVATNumberException(
                InvalidVATNumberException::invalidFormat()
            );
        }

        $this->validateSpecificCountry();
    }

    private function validateSpecificCountry(): void
    {
        match ($this->countryCode) {
            'FR' => $this->validateFrench(),
            'GP' => $this->validateBritish(),
            default => null
        };
    }

    private function validateFrench(): void
    {
        // Format FR : FRxx xxxxxxxxx (2 lettres/digits + 9 digits)
        if (!preg_match('/^FR[A-Z0-9]{2}\d{9}$/', $this->value)) {
            throw new InvalidVATNumberException(
                InvalidVATNumberException::invalidFRFormat()
            );
        }
    }

    private function validateBritish(): void
    {
        // Format GB : GB123456789 ou GB123456789012
        if (!preg_match('/^GB(\d{9}|\d{12})$/', $this->value)) {
            throw new InvalidVATNumberException(
                InvalidVATNumberException::invalidGPFormat()
            );
        }
    }
}
