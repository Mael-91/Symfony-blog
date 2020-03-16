<?php

namespace App\DataFixtures;

use App\Entity\BlogComment as BlogCommentEntity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class BlogComment extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $comment = new BlogCommentEntity();
        $comment->setPost('5');
        $comment->getAuthor('Mael Constantin');
        $comment->getContent('Mon commentaire');
        $comment->setCreatedAt(new \DateTime());
        $comment->getVisible(true);
        $this->manager->persist($comment);

        $comment = new BlogCommentEntity();
        $comment->setPost('5');
        $comment->getAuthor('Mael Constantin');
        $comment->getContent('Mon commentaire');
        $comment->setCreatedAt(new \DateTime());
        $comment->getVisible(true);
        $this->manager->persist($comment);

        $comment = new BlogCommentEntity();
        $comment->setPost('5');
        $comment->getAuthor('Mael Constantin');
        $comment->getContent('Mon commentaire');
        $comment->setCreatedAt(new \DateTime());
        $comment->getVisible(true);
        $this->manager->persist($comment);

        $manager->flush();
    }
}
