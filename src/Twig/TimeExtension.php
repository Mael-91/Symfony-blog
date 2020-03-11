<?php


namespace App\Twig;


use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TimeExtension extends AbstractExtension {

    public function getFilters()
    {
        return [
            new TwigFilter('ago', [$this, 'ago'], ['is_safe' => ['html']])
        ];
    }

    /**
     * Permet d'envoyer à la vue, une date formatée
     * @param \DateTime $date
     * @param string|null $addClass
     * @param string $format
     * @return string
     */
    public function ago(\DateTime $date, string $addClass = null, string $format = 'd/m/Y H:i') {
        return '<span class="timeago '. $addClass . '" datetime="' . $date->format(\DateTime::ISO8601) . '">' . $date->format($format) .'</span>';
    }
}