<?php

namespace App\Tests;

use App\Twig\TextExtension;
use PHPUnit\Framework\TestCase;

class TextExtensionTest extends TestCase
{
    /**
     * @var TimeExtension
     */
    private $textExention;

    public function setUp() {
        $this->textExention = new TextExtension();
    }

    public function testTruncateWithShortText() {
        $text = 'Voici un texte';
        $this->assertEquals('Voici un texte', $this->textExention->truncate($text, 20));

    }

    public function testTruncateWithLongText() {
        $text = 'Voici un texte long qui sera coupé';
        $this->assertEquals('Voici ...', $this->textExention->truncate($text, 6));
    }
}
