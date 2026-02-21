<?php

namespace Tokirak\InvoiceGenerator\Infrastructure\Generator;

use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Tokirak\InvoiceGenerator\Domain\Model\Entity\Invoice;
use Tokirak\InvoiceGenerator\Domain\Model\ValueObject\Locale;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class PdfGenerator
{
    private Environment $twig;
    private array $config;
    /**
     * @param Environment $twig
     * @param array $config
     */
    public function __construct(
        Environment $twig,
        array $config
    ) {
        $this->twig = $twig;
        $this->config = array_merge([
            'orientation' => 'portrait',
            'paper_size' => 'A4',
            'templates' => [
                'fr_FR' => '@InvoiceGenerator/invoice.html.twig',
                'en_GB' => '@InvoiceGenerator/invoice.html.twig'
            ],
        ], $config);
    }


    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function generate(Invoice $invoice, Locale $locale = Locale::FR_FR): string
    {
        $templateFile = $this->config['templates'][$locale->value];

        // render html contents
        $html = $this->twig->render($templateFile, [
            'invoice' => $invoice,
            'locale' => $locale,
        ]);


        // generate pdf
        $options = new Options();
        $options->set('isHTML5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper($this->config['paper_size'], $this->config['orientation']);
        $dompdf->render();

        return $dompdf->output();
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function save(Invoice $invoice, string $path, Locale $locale = Locale::FR_FR): void
    {
        $pdf = $this->generate($invoice, $locale);
        file_put_contents($path, $pdf);
    }
}
