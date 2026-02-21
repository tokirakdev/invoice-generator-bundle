<?php

namespace Tokirak\InvoiceGenerator;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Tokirak\InvoiceGenerator\DependencyInjection\InvoiceGeneratorExtension;

class InvoiceGeneratorBundle extends AbstractBundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new InvoiceGeneratorExtension();
    }
}
