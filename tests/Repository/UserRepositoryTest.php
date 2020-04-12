<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\AssertTrait;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{

    use FixturesTrait;
    use AssertTrait;

    public function testSuccessrUpgradePassword()
    {
        self::bootKernel();
        $newPass = '$argon2id$v=19$m=65536,t=4,p=1$Im/OYZ5OiQYmbVMLwczVLA$56+1Si/tFhbbCCVMG9YNAHpjlbd98yknMK8YZJ3QW7Y';
        $repo = self::$container->get(UserRepository::class)->upgradePassword($this->makeUser(), $newPass);
        $this->assertNull($repo);
    }

    public function testCountUser()
    {
        $this->assertEqualsMethodRepo('countUser', 18, 'User', UserRepository::class);
    }

    public function testCountWithOAuth()
    {
        $this->assertEqualsMethodRepo('countUserWithOAuth', 5, 'User', UserRepository::class);
    }

    public function testFindLastUser()
    {
        $this->assertCountMethodRepo('findLastUser', 5, 'User', UserRepository::class);
    }

    public function testFindUserByUsernameOrEmail()
    {
        self::bootKernel();
        $this->loadFixtureFiles([
            dirname(__DIR__, 1) . "/fixtures/User.yaml"
        ]);
        $repo = self::$container->get(UserRepository::class)->findUserByUsernameOrEmail('mael@gmail.com');
        $this->assertInstanceOf(User::class, $repo);
    }

    private function makeUser() {
        return (new User())
            ->setUsername('Mael55')
            ->setPassword('$argon2id$v=19$m=65536,t=4,p=1$O38Nld+foXR3a/ZYCHxtvw$7bY2/f09beGeDPANyASpmTkzUJIlQcYD7YZi4H7PaAU')
            ->setEmail('mmm@gmail.com')
            ->setBirthday(new \DateTime('24-09-2003'))
            ->setSexe(1)
            ->setAvatarFilename('avatar.jpg')
            ->setBannerFilename('banner.jpg');
    }
}