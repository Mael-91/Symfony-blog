<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class MarkdownGuideControllerTest extends WebTestCase {

    public function testGuidePage() {
        $client = static::createClient();
        $client->request('GET', '/guide/markdown');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
