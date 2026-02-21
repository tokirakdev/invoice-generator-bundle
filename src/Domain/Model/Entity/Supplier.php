<?php

declare(strict_types=1);

namespace Tokirak\InvoiceGenerator\Domain\Model\Entity;

use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\Address;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\CompanyIdentifier\CompanyIdentifier;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\VATNumber;

final class Supplier
{
    public function __construct(
        public readonly string $name,
        public readonly Address $address,
        public readonly CompanyIdentifier $companyId,
        public readonly ?VATNumber $VATNumber = null
    ) {}
}
