<?php

namespace Tokirak\InvoiceGenerator\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\Currency;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\Locale;

class Configuration implements ConfigurationInterface
{
    #[\Override] public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('invoice_generator');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode->children()
            ->enumNode('locale')
            ->values(Locale::cases())
            ->defaultValue(Locale::FR_FR)
            ->end();

        $rootNode
            ->children()
            ->arrayNode('templates')
            ->scalarPrototype()->end()      // valeur = string
            ->defaultValue([
                'fr_FR' => '@InvoiceGenerator/invoice_fr.html.twig',
                'en_GB' => '@InvoiceGenerator/invoice_en.html.twig',
            ])
            ->end()
            ->end();


        return $treeBuilder;
    }
}
