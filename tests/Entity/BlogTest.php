<?php

namespace App\Tests;

use App\Entity\Blog;
use App\Entity\BlogCategory;
use App\Entity\User;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;

class BlogTest extends KernelTestCase {

    use FixturesTrait;
    use AssertTrait;

    public function testValidEntity() {
        $this->assertHasError($this->getEntity(), 0);
    }

    public function testBlankTitle() {
        $this->assertHasError($this->getEntity()->setTitle(''), 1);
    }

    public function testTitleAlreadyExist() {
        $this->loadFixtureFiles([
           dirname(__DIR__, 1) . '/fixtures/Blog.yaml'
        ]);
        $this->assertHasError($this->getEntity()->setTitle('test'), 1);
    }

    public function testInvalidBlankContentEntity() {
        $this->assertHasError($this->getEntity()->setContent(''), 1);
    }

    public function testBlankPictureFile() {
        $this->assertHasError($this->getEntity()->setPictureFilename(''), 1);
    }

    public function testAcceptBlankBanner() {
        $this->assertHasError($this->getEntity()->setBannerFilename(''), 0);
    }

    private function getEntity(): Blog {
        return (new Blog())
            ->setTitle('Test blog')
            ->setContent('Le contenu de l\'article')
            ->setPictureFilename('picture.jpg')
            ->setBannerFilename('banner.jpg')
            ->setAuthor($this->makeUser())
            ->setCategory($this->makeCategory());
    }



    private function makeUser() {
        $user = new User();
        $password = '$argon2id$v=19$m=65536,t=4,p=1$F3JG29Imj3W9Nz0paHcxHA$OpYmPkCOhiA3/r7ilr00SOYLbfP9ZfcUCz60GLxVmH0';
        $user->setUsername('Mael')
            ->setRoles(['ROLE_SUPER_ADMIN'])
            ->setPassword($password)
            ->setEmail('mael.constantin@gmail.com')
            ->setFirstName('Mael')
            ->setLastName('Constantin')
            ->setBirthday(new \DateTime('24-09-2003'))
            ->setSexe(1)
            ->setEnabled(true)
            ->setAvatarFilename('avatar.png')
            ->setBannerFilename('test_banner.jpg');
        return $user;
    }

    private function makeCategory() {
        $category = new BlogCategory();
        $category->setName('première catégorie');
        return $category;
    }
}
