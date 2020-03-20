<?php

namespace App\DataFixtures;

use App\Entity\Blog;
use App\Entity\BlogCategory;
use App\Entity\BlogComment;
use App\Entity\BlogLike;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder) {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $users = [];
        $posts = [];
        $categories = [];

        $user = new User();
        $user->setUsername('admin')
            ->setEmail('admin@admin.com')
            ->setPassword($this->encoder->encodePassword($user, 'M@eL91220'))
            ->setFirstName('Mael')
            ->setLastName('Constantin')
            ->setSexe('1')
            ->setRoles(['ROLE_SUPER_ADMIN'])
            ->setCreatedAt(new \DateTime())
            ->setEnabled(true);
        $manager->persist($user);

        $users[] = $user;

        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setUsername($faker->userName)
                ->setEmail($faker->email)
                ->setPassword($this->encoder->encodePassword($user, $faker->password))
                ->setFirstName($faker->firstName)
                ->setLastName($faker->lastName)
                ->setSexe(rand(1, 2))
                ->setRoles(['ROLE_USER'])
                ->setCreatedAt(new \DateTime())
                ->setEnabled(true);
            $manager->persist($user);
            $users[] = $user;
        }

        for ($i = 0; $i < 15; $i++) {
            $category = new BlogCategory();
            $category->setName($faker->sentence);
            $manager->persist($category);
            $categories[] = $category;
        }

        for ($i = 0; $i < 100; $i++) {
            $post = new Blog();
            $post->setTitle($faker->sentence)
                ->setContent($faker->paragraph)
                ->setAuthor($faker->randomElement($users))
                ->setCategory($faker->randomElement($categories))
                ->setCreatedAt(new \DateTime())
                ->setActive(true);
            $manager->persist($post);
            $posts[] = $post;

            for ($c = 0; $c < 20; $c++) {
                $comment = new BlogComment();
                $comment->setPost($faker->randomElement($posts))
                    ->setAuthor($faker->randomElement($users))
                    ->setContent($faker->paragraph)
                    ->setVisible(true)
                    ->setCreatedAt(new \DateTime());
                $manager->persist($comment);
            }

            for ($j = 0; $j < mt_rand(0, 1200); $j++) {
                $like = new BlogLike();
                $like->setUser($faker->randomElement($users))
                    ->setPost($faker->randomElement($posts));
                $manager->persist($like);
            }
        }

        $manager->flush();
    }
}
