<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class GuideControllerTest extends WebTestCase {

    public function testGuidePage() {
        $client = static::createClient();
        $client->request('GET', '/guide');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
