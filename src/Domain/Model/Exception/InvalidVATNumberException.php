<?php

namespace Tokirak\InvoiceGenerator\Domain\Model\Exception;

class InvalidVATNumberException extends \DomainException
{
    public static function Length(): string
    {
        return 'VAT Number too short';
    }

    public static function invalidFormat(): string
    {
        return 'Invalid VAT number format. Expected: 2 letters + 2-13 alphanumeric';
    }
    public static function invalidFRFormat(): string
    {
        return 'Invalid French VAT format. Expected: FRxx 123456789';
    }
    public static function invalidGPFormat(): string
    {
        return 'Invalid UK VAT format. Expected: GB123456789';
    }
}
