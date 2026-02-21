<?php

namespace Tokirak\Tests\Integration;

use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;
use Twig\Loader\FilesystemLoader;

class ENIntegrationTestCase extends AbstractIntegrationTestCase
{
    #[\Override] public function createTranslator(): void
    {
        // setting up translator
        $this->translator = new Translator('en');
        $this->translator->addLoader('yaml', new YamlFileLoader());

        // adding translations files
        $translatorFilePath =  '/var/www/html/translations/messages.en.yaml';

        if (file_exists($translatorFilePath)) {
            $this->translator->addResource(
                'yaml',
                $translatorFilePath,
                'en'
            );
        }
    }

    #[\Override] public function createFileLoader(): void
    {
        $this->loader = new FilesystemLoader('/var/www/html/templates');
    }
}
