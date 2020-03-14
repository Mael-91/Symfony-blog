<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DashboardManageBlogPostTest extends WebTestCase
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

    public function testManagePostRedirect() {
        $this->client->request('GET', '/dashboard/blog/manage/post');

        static::assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function testPostCreate() {
        $crawler = $this->client->request('GET', '/dashboard/blog/manage/post/create');

        $form = $crawler->selectButton('Publier')->form();
        $form['blog[title]'] = 'John Doe new article';
        $form['blog[author]'] = 'Mael Constantin';
        $form['blog[content]'] = 'Voici un article de test';
        $form['blog[active]'] = true;
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        static::assertEquals(1, $crawler->filter('div.alert.alert-success')->count());
    }

    public function testPostEdit() {
        $this->client->request('GET', '/dashboard/blog/manage/post/edit/7');
        $this->client->submitForm('Éditer', [
            'blog[title]' => 'John Doe article 1',
            'blog[author]' => 'Mael Constantin',
            'blog[content]' => 'Voici un article de test',
            'blog[active]' => true,
        ]);
        $crawler = $this->client->followRedirect();
        static::assertEquals(1, $crawler->filter('div.alert.alert-success')->count());
    }
}
