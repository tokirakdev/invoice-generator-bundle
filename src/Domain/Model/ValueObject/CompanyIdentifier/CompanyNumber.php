<?php

declare(strict_types=1);

namespace Tokirak\InvoiceGenerator\Domain\Model\ValueObject\CompanyIdentifier;

use Tokirak\InvoiceGenerator\Domain\Model\Exception\InvalidCompanyNumberException;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\Locale;

/**
 * class for UK CompanyIdentifier
 */
final class CompanyNumber extends CompanyIdentifier
{
    #[\Override] protected function validate(): void
    {
        // Format UK: 8 digits or 2 letters + 6 digits
        if (!preg_match('/^([A-Z]{2}\d{6}|\d{8})$/', $this->value)) {
            throw new InvalidCompanyNumberException(
                InvalidCompanyNumberException::InvalidNumber()
            );
        }
    }

    #[\Override] public function format(): string
    {
        return $this->value;
    }

    #[\Override] public function getLabel(Locale $locale): string
    {
        return match ($locale) {
            Locale::FR_FR => 'Numéro d\'entreprise (UK)',
            default => 'Company Number'
        };
    }
}
