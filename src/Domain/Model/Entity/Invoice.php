<?php

declare(strict_types=1);

namespace Tokirak\InvoiceGenerator\Domain\Model\Entity;

use Tokirak\InvoiceGenerator\Domain\Model\Exception\InvalidInvoiceException;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\Amount;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\Currency;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\InvoiceNumber;

final class Invoice
{
    /** @var InvoiceLine[]  */
    private array $lines = [];

    public function __construct(
        public readonly InvoiceNumber $invoiceNumber,
        public readonly Supplier $supplier,
        public readonly Customer $customer,
        public readonly \DateTimeImmutable $issuedAt,
        public readonly \DateTimeImmutable $dueAt,
        public readonly Currency $currency = Currency::EUR
    ) {}

    public function addLine(InvoiceLine $line): self
    {
        if (!$line->unitePriceHT->currency->equals($this->currency)) {
            throw new InvalidInvoiceException(
                "Line currency must match invoice currency: {$this->currency->value}"
            );
        }

        $this->lines[] = $line;

        return $this;
    }
    public function getLines(): array
    {
        return $this->lines;
    }

    public function calculateTotalHT(): Amount
    {
        return array_reduce(
            $this->lines,
            fn($total, $item) => $total->add($item->calculateTotalHT()),
            Amount::zero($this->currency)
        );

    }

    public function calculateTotalVAT(): Amount
    {
        return array_reduce(
            $this->lines,
            fn($total, $item) => $total->add($item->calculateVAT()),
            Amount::zero($this->currency)
        );
    }

    public function calculateTotalTTC(): Amount
    {
        return array_reduce(
            $this->lines,
            fn($total, $item) => $total->add($item->calculateTotalTTC()),
            Amount::zero($this->currency)
        );
    }

    public static function create(
        InvoiceNumber $invoiceNumber,
        Supplier $supplier,
        Customer $customer,
        \DateTimeImmutable $issueAt,
        \DateTimeImmutable $dueAt,
        Currency $currency = Currency::EUR
    ): self {
        return new self($invoiceNumber, $supplier, $customer, $issueAt, $dueAt, $currency);
    }

    public function getVATBreakdown(): array
    {
        $breakdown = [];

        foreach ($this->lines as $line) {
            $rate = $line->VATRate;
            $rateKey = $rate->toPercentage();

            if (!isset($breakdown[$rateKey])) {
                $breakdown[$rateKey] = [
                    'rate' => $rate,
                    'base' => Amount::zero($this->currency),
                    'vat' => Amount::zero($this->currency),
                ];
            }

            $breakdown[$rateKey]['base'] = $breakdown[$rateKey]['base']->add($line->calculateTotalHT());
            $breakdown[$rateKey]['vat'] = $breakdown[$rateKey]['vat']->add($line->calculateVAT());
        }

        return $breakdown;
    }
}
