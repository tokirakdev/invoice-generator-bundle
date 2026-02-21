<?php

declare(strict_types=1);

namespace Tokirak\InvoiceGenerator\Domain\Model\ValueObject;

use Tokirak\InvoiceGenerator\Domain\Model\Exception\InvalidInvoiceNumberException;

final readonly class InvoiceNumber
{
    private function __construct(
        public string $value
    ) {
        if (empty($value)) {
            throw new InvalidInvoiceNumberException('Invoice number cannot be empty');
        }
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public static function generate(
        string $prefix,
        int $year,
        int $counter,
        int $paddingLength = 4
    ): self {
        $counterStr = str_pad((string) $counter, $paddingLength, '0', STR_PAD_LEFT);
        $value = "{$prefix}-{$year}-{$counterStr}";

        return new self($value);
    }

    public function format(): string
    {
        return $this->value;
    }
}
