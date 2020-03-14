<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DashboardBlogCategoriesControllerTest extends WebTestCase
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

    public function testManageCategoryRedirect() {
        $this->client->request('GET', '/dashboard/blog/manage/categories');

        static::assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }



    public function testCategoryCreate() {
        $crawler = $this->client->request('GET', '/dashboard/blog/manage/categories/create');

        $form = $crawler->selectButton('Créer la catégorie')->form();
        $form['blog_category[name]'] = 'John Doe category';
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        static::assertEquals(1, $crawler->filter('div.alert.alert-success')->count());
    }

    public function testCategoryEdit() {
        $this->client->request('GET', '/dashboard/blog/manage/categories/edit/6');
        $this->client->submitForm('Éditer', [
            'blog_category[name]' => 'John Doe category 1'
        ]);
        $crawler = $this->client->followRedirect();
        static::assertEquals(1, $crawler->filter('div.alert.alert-success')->count());
    }
}
