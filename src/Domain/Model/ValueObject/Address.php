<?php

declare(strict_types=1);

namespace Tokirak\InvoiceGenerator\Domain\Model\ValueObject;

use Tokirak\InvoiceGenerator\Domain\Model\Exception\InvalidAddressException;

final readonly class Address
{
    public function __construct(
        public string $street,
        public string $zipCode,
        public string $city,
        public ?string $state = null,        // for US, CA, etc.
        public ?string $country = null
    ) {
        if (empty($street) || empty($zipCode) || empty($city)) {
            throw new InvalidAddressException('Street, zipCode and city are required');
        }
    }

    public function format(Locale $locale = Locale::FR_FR): string
    {
        return match ($locale) {
            Locale::FR_FR => $this->formatFrench(),
            Locale::EN_GB => $this->formatBritish(),
            default => $this->formatGeneric()
        };
    }

    private function formatFrench(): string
    {
        return "{$this->street}\n{$this->zipCode} {$this->city}";
    }

    private function formatBritish(): string
    {
        return "{$this->street}\n{$this->city}\n{$this->zipCode}";
    }

    private function formatGeneric(): string
    {
        $parts = [$this->street, $this->zipCode . ' ' . $this->city];

        if ($this->state) {
            $parts[] = $this->state;
        }

        if ($this->country) {
            $parts[] = $this->country;
        }

        return implode("\n", $parts);
    }
}
