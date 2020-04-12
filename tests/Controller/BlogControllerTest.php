<?php

namespace App\Tests;

use App\Entity\Blog;
use App\Entity\BlogCategory;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class BlogControllerTest extends WebTestCase {

    use FixturesTrait;

    public function testBlogPage() {
        $client = static::createClient();
        $client->request('GET', '/blog');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testBlogCategoryPage() {
        $client = static::createClient();
        $categories = $this->loadFixtureFiles([
            dirname(__DIR__, 1) . '/fixtures/BlogCategory.yaml'
        ]);
        /** @var BlogCategory $category */
        $category = $categories['categoryTest'];
        $client->request('GET', "/blog/category/{$category->getSlug()}");
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testShowPost() {
        $client = static::createClient();
        $posts = $this->loadFixtureFiles([
            dirname(__DIR__, 1) . '/fixtures/Blog.yaml'
        ]);
        /** @var Blog $post */
        $post = $posts['articleTest'];
        $client->request('GET', "/blog/{$post->getSlug()}-{$post->getId()}");
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testShowPostinCategoryPage() {
        $client = static::createClient();
        $data = $this->loadFixtureFiles([
            dirname(__DIR__, 1) . '/fixtures/BlogCategory.yaml',
            dirname(__DIR__, 1) . '/fixtures/Blog.yaml'
        ]);
        /** @var BlogCategory $category */
        $category = $data['categoryTest'];
        /** @var Blog $post */
        $post = $data['articleTest'];
        $client->request('GET', "/blog/category/{$category->getSlug()}");
        $linkToPost = $client->clickLink('Article de teste')->getUri();
        $linkToPost = substr($linkToPost, 16);
        $client->request('GET', $linkToPost);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
