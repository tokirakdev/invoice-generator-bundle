<?php

declare(strict_types=1);

namespace Tokirak\InvoiceGenerator\Domain\Model\ValueObject\CompanyIdentifier;

use Tokirak\InvoiceGenerator\Domain\Model\Exception\InvalidSiretException;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\Locale;

/**
 * Class for France Company Identifier
 */
final class Siret extends CompanyIdentifier
{
    #[\Override] protected function validate(): void
    {
        // 14 digits for SIRET FR
        if (!preg_match('/^\d{14}$/', $this->value)) {
            throw new InvalidSiretException(InvalidSiretException::LENGTH);
        }
    }

    #[\Override] public function format(): string
    {
        // Format : 123 456 789 01234
        return substr($this->value, 0, 3) . ' '
            . substr($this->value, 3, 3) . ' '
            . substr($this->value, 6, 3) . ' '
            . substr($this->value, 9);
    }

    #[\Override] public function getLabel(Locale $locale): string
    {
        return match ($locale) {
            Locale::EN_GB => 'Company ID (SIRET)',
            Locale::DE_DE => 'Handelsregisternummer (SIRET)',
            default => 'SIRET',
        };
    }

    public function getSIREN(): string
    {
        // SIREN = 9 first digits of siret
        return substr($this->value, 0, 9);
    }
}
