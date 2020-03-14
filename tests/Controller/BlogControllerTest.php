<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class BlogControllerTest extends WebTestCase
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\KernelBrowser
     */
    private $client;

    public function setUp() {
        $this->client = static::createClient();
    }

    public function testBlogRedirect() {
        $this->client->request('GET', '/blog');

        static::assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function testPostShow() {
        $crawler = $this->client->request('GET', '/blog/mon-premier-article-5');

        static::assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        static::assertEquals(1, $crawler->filter('h5')->count());
        static::assertSame('Mon premier article', $crawler->filter('.card-title')->text());
    }

    public function testPostRedirectWithBadSlug() {
        $this->client->request('GET', '/blog/mon-premier-artcle-5');
        static::assertEquals(Response::HTTP_MOVED_PERMANENTLY, $this->client->getResponse()->getStatusCode());
        $this->client->request('GET', '/blog/mon-premier-article-1');
        static::assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function testCategoryAssociate() {
        $crawler = $this->client->request('GET', '/blog/mon-premier-article-5');

        static::assertSame('première catégorie', $crawler->filter('div.datetime > a.text-decoration-none')->text());
        static::assertSame('http://localhost/blog/category/premiere-categorie', $crawler->filter('a.text-decoration-none.text-muted')->link()->getUri());
    }

    public function testCategoryRedirect() {
        $this->client->request('GET', '/blog/category/premiere-categorie');

        static::assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }
}
