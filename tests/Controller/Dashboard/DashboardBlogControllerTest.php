<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DashboardBlogControllerTest extends WebTestCase
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\KernelBrowser
     */
    private $client;

    public function setUp() {
        $this->client = static::createClient([], [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => 'M@eL91220',
            'Roles' => ['ROLE_SUPER_ADMIN']
        ]);
    }

    public function testDashboardBlogRedirect() {
        $this->client->request('GET', '/dashboard/blog');

        static::assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }
}
