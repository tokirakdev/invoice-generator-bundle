<?php

namespace Tokirak\InvoiceGenerator\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Twig\Extra\Intl\IntlExtension;

class InvoiceGeneratorExtension extends Extension
{

    #[\Override] public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader(
          $container,
          new FileLocator(__DIR__ . '/../../config')
        );

        $container->setParameter('invoice_generator.templates', ['templates' => $config['templates']]);
        $container->setParameter('invoice_generator.locale', $config['locale']);


        $loader->load('services.yaml');

        if(class_exists(IntlExtension::class)) {
            $container->register(IntlExtension::class)
                ->addTag('twig.extension');
        }
    }
}
