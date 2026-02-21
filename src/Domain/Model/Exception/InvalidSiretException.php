<?php

namespace Tokirak\InvoiceGenerator\Domain\Model\Exception;

class InvalidSiretException extends \DomainException
{
    public const LENGTH = 'SIRET must be exactly 14 digits';
}
