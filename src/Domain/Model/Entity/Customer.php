<?php

declare(strict_types=1);

namespace Tokirak\InvoiceGenerator\Domain\Model\Entity;

use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\Address;

final class Customer
{
    public function __construct(
        public readonly string $name,
        public readonly Address $address
    ) {}
}
