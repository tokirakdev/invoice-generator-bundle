<?php

namespace Tokirak\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;
use Twig\Environment;
use Twig\Extra\Intl\IntlExtension;
use Twig\Loader\FilesystemLoader;

abstract class AbstractIntegrationTestCase extends TestCase
{
    protected Environment $twig;
    protected Translator $translator;
    protected FilesystemLoader $loader;

    abstract public function createTranslator(): void;
    abstract public function createFileLoader(): void;


    protected function setUp(): void
    {
        $this->createTranslator();
        $this->createFileLoader();
        $this->twig = new Environment($this->loader, [
            'cache' => false,
            'strict_variables' => true,
            'debug' => true,
        ]);
        // add extensions
        $this->twig->addExtension(new TranslationExtension($this->translator));
        $this->twig->addExtension(new IntlExtension());
    }
}
