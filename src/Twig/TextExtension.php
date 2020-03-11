<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TextExtension extends AbstractExtension {

    public function getFilters() {
        return [
            new TwigFilter('truncate', [$this, 'truncate'])
        ];
    }

    public function truncate(?string $content, int $maxLenght = 200): string {
        if (is_null($content)) {
            return '';
        }

        if (mb_strlen($content) > $maxLenght) {
            $truncate = mb_substr($content, 0, $maxLenght);
            $lastSpace = mb_strripos($truncate, ' ');
            return mb_substr($truncate, 0, $lastSpace) . ' ...';
        }
        return $content;
    }
}