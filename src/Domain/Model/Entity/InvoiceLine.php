<?php

declare(strict_types=1);

namespace Tokirak\InvoiceGenerator\Domain\Model\Entity;

use Tokirak\InvoiceGenerator\Domain\Model\Exception\InvalidInvoiceLineException;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\Amount;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\VATRate;

final class InvoiceLine
{
    public function __construct(
        public readonly string $description,
        public readonly float $quantity,
        public readonly Amount $unitePriceHT,
        public readonly VATRate $VATRate
    ) {
        if ($this->quantity <= 0) {
            throw new InvalidInvoiceLineException('Quantity must be positive');
        }
    }

    public function calculateTotalHT(): Amount
    {
        return $this->unitePriceHT->multiply($this->quantity);
    }
    public function calculateVAT(): Amount
    {
        return $this->unitePriceHT->multiply($this->VATRate->value());
    }

    public function calculateTotalTTC(): Amount
    {
        return $this->calculateTotalHT()->add($this->calculateVAT());
    }

    public static function create(
        string $description,
        float $quantity,
        Amount $unitPriceHT,
        VATRate $VATRate
    ): self {
        return new self($description, $quantity, $unitPriceHT, $VATRate);
    }
}
