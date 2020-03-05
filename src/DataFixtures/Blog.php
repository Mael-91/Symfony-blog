<?php

namespace App\DataFixtures;

use App\Entity\Blog as BlogEntity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class Blog extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $blog = new BlogEntity();
        $blog->setTitle('Mon premier article');
        $blog->setAuthor('Mael Constantin');
        $blog->setContent('Mon premier article');
        $blog->setActive(1);
        $manager->persist($blog);
        $manager->flush();
    }
}
