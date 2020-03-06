<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class User extends Fixture
{

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new \App\Entity\User();
        $user->setUsername('admin');
        $user->setEmail('admin@gmail.com');
        $user->setFirstName('Mael');
        $user->setLastName('Constantin');
        $user->setBirthday(new \DateTime('now'));
        $user->setSexe(1);
        $user->setRoles((array)'ROLE_ADMIN');
        $user->setCreatedAt(new \DateTime('now'));
        $user->setPassword($this->encoder->encodePassword($user, 'admin'));
        $manager->persist($user);
        $manager->flush();
    }
}
