<?php

namespace Tokirak\InvoiceGenerator\Domain\Model\Exception;

class InvalidCompanyNumberException extends \DomainException
{
    public static function InvalidNumber(): string
    {
        return 'Invalid UK Company Number. Expected: 8 digits or 2 letters + 6 digits';
    }
}
