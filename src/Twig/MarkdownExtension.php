<?php

namespace App\Twig;

use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MarkdownExtension extends AbstractExtension
{

    /**
     * @var MarkdownParserInterface
     */
    private $parser;

    public function __construct(MarkdownParserInterface $parser) {
        $this->parser = $parser;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('markdown_converter', [$this, 'markdownConverter'], ['is_safe' => ['html']]),
            new TwigFilter('unformatted_text', [$this, 'unformatted']),
        ];
    }

    public function markdownConverter(string $text): string {
        $text = $this->parser->transformMarkdown($text);
        return $text;
    }

    public function unformatted(string $text): string {
        $symbols = ['#', '-', '*', '_', '>', '`', '|'];
        $text = str_replace($symbols, '', $text);
        return $text;
    }
}
