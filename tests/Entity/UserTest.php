<?php

namespace App\Tests;

use App\Entity\User;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;

class UserTest extends KernelTestCase {

    use FixturesTrait;
    use AssertTrait;

    public function testValidEntity() {
        $this->assertValidatorErrors($this->getEntity(), 0);
    }

    public function testUserWithUsernameAlreadyTaken() {
        $this->loadFixtureFiles([
            dirname(__DIR__, 1) . '/fixtures/User.yaml'
        ]);
        $this->assertValidatorErrors($this->getEntity()->setUsername('Test1'), 1);
    }

    public function testUserWithEmailAlreadyTaken() {
        $this->loadFixtureFiles([
            dirname(__DIR__, 1) . '/fixtures/User.yaml'
        ]);
        $this->assertValidatorErrors($this->getEntity()->setEmail('used@email.com'), 1);
    }

    public function testValidEmail() {
        $this->assertValidatorErrors($this->getEntity()->setEmail('myemail.com'), 1);
    }

    public function testValidFirstName() {
        $this->assertValidatorErrors($this->getEntity()->setFirstName('Mael'), 0);
    }

    public function testValidFirstNameWithCompoundNoun() {
        $this->assertValidatorErrors($this->getEntity()->setFirstName('Jean-Michel'), 0);
    }

    public function testValidFirstNameWithAccent() {
        $this->assertValidatorErrors($this->getEntity()->setFirstName('Maël'), 0);
        $this->assertValidatorErrors($this->getEntity()->setFirstName('MaËl'), 0);
        $this->assertValidatorErrors($this->getEntity()->setFirstName('Maél'), 0);
        $this->assertValidatorErrors($this->getEntity()->setFirstName('Maèl'), 0);
        $this->assertValidatorErrors($this->getEntity()->setFirstName('MaÉl'), 0);
        $this->assertValidatorErrors($this->getEntity()->setFirstName('MaÈl'), 0);
        $this->assertValidatorErrors($this->getEntity()->setFirstName('Mïl'), 0);
        $this->assertValidatorErrors($this->getEntity()->setFirstName('MÏl'), 0);
        $this->assertValidatorErrors($this->getEntity()->setFirstName('ameçon'), 0);
    }

    public function testBlankFistName() {
        $this->assertValidatorErrors($this->getEntity()->setFirstName(''), 1);
    }

    public function testInvalidFirstName() {
        $this->assertValidatorErrors($this->getEntity()->setFirstName('Mael91'), 1);
    }

    public function testValidName() {
        $this->assertValidatorErrors($this->getEntity()->setFirstName('Constantin'), 0);
    }

    public function testValidNameWithCompoundNoun() {
        $this->assertValidatorErrors($this->getEntity()->setFirstName('Dubois-Saint-André'), 0);
    }

    public function testBlankName() {
        $this->assertValidatorErrors($this->getEntity()->setFirstName(''), 1);
    }

    public function testInvalidName() {
        $this->assertValidatorErrors($this->getEntity()->setFirstName('Dubois-Saint-André99'), 1);
    }

    public function testInvalidFirstNameWithAccent() {
        $this->assertValidatorErrors($this->getEntity()->setFirstName('Maàl'), 1);
        $this->assertValidatorErrors($this->getEntity()->setFirstName('MaÄl'), 1);
        $this->assertValidatorErrors($this->getEntity()->setFirstName('Maâl'), 1);
        $this->assertValidatorErrors($this->getEntity()->setFirstName('MaÀl'), 1);
        $this->assertValidatorErrors($this->getEntity()->setFirstName('MaÙl'), 1);
        $this->assertValidatorErrors($this->getEntity()->setFirstName('MaÇl'), 1);
        $this->assertValidatorErrors($this->getEntity()->setFirstName('MŒl'), 1);
        $this->assertValidatorErrors($this->getEntity()->setFirstName('Mœl'), 1);
    }

    private function getEntity() {
        return (new User())
            ->setUsername('aeaze')
            ->setRoles(['ROLE_USER'])
            ->setPassword('$argon2id$v=19$m=65536,t=4,p=1$jQIrFZLkbUPsdzeWlup4Sw$GRifpLVpD4T15RQDqkvvLi+AcvA6kO8f4KGVteaob7Y')
            ->setEmail('test@test.com')
            ->setFirstName('Teeest')
            ->setLastName('Unnit')
            ->setBirthday(new \DateTime('24-09-2003'))
            ->setSexe(1)
            ->setEnabled(true)
            ->setAvatarFilename('avatar.png')
            ->setBannerFilename('test_banner.jpg');
    }
}
