<?php

namespace App\Tests;

use App\Entity\BlogCategory;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;

class BlogCategoryTest extends KernelTestCase {

    use FixturesTrait;
    use AssertTrait;

    public function testValidEntity() {
        $this->assertHasError($this->getEntity(), 0);
    }

    public function testBlankCategoryName() {
        $this->assertHasError($this->getEntity()->setName(''), 1);
    }

    public function testCategoryNameAlreadyExist() {
        $this->loadFixtureFiles([
            dirname(__DIR__, 1) . '/fixtures/BlogCategory.yaml'
        ]);
        $this->assertHasError($this->getEntity()->setName('première catégorie'), 1);
    }

    private function getEntity(): BlogCategory {
        return (new BlogCategory())
            ->setName('première catégory');
    }
}
