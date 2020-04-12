<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class HomeControllerTest extends WebTestCase {

    public function testHomePage() {
        $client = static::createClient();
        $client->request('GET', '');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testHomePageContainH1() {
        $client = static::createClient();
        $client->request('GET', '');
        $this->assertSelectorTextContains('h1', 'Bienvenue sur le blog');
    }
}
