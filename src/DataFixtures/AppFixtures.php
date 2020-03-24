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
        $parents = [];

        // Création de l'utilisateur admin
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

        // Création d'utilisateurs aléatoires
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

        // Création de catégories aléatoires
        for ($i = 0; $i < 5; $i++) {
            $category = new BlogCategory();
            $category->setName($faker->sentence);
            $manager->persist($category);
            $categories[] = $category;
        }

        // Création d'articles aléatoire
        for ($i = 0; $i < 15; $i++) {
            $post = new Blog();
            $post->setTitle($faker->sentence)
                ->setContent($faker->paragraph(5))
                ->setAuthor($faker->randomElement($users))
                ->setPictureFilename('5e754250eaa58935050116.jpg')
                ->setBannerFilename('5e75f4178c33c367527604.jpg')
                ->setCategory($faker->randomElement($categories))
                ->setCreatedAt(new \DateTime())
                ->setActive(true);
            $manager->persist($post);
            $posts[] = $post;

            // Création de commentaires parents aléatoire
            for ($c = 0; $c < 5; $c++) {
                $comment = new BlogComment();
                $comment->setPost($faker->randomElement($posts))
                    ->setAuthor($faker->randomElement($users))
                    ->setContent($faker->paragraph)
                    ->setVisible(true)
                    ->setCreatedAt(new \DateTime());
                $manager->persist($comment);
                $parents[] = $comment;
            }

            for ($r = 0; $r < 5; $r++) {
                $reply = new BlogComment();
                $reply->setPost($faker->randomElement($posts))
                    ->setAuthor($faker->randomElement($users))
                    ->setContent($faker->paragraph)
                    ->setVisible(true)
                    ->setCreatedAt(new \DateTime())
                    ->setParent($faker->randomElement($parents));
                $manager->persist($reply);
            }

            // Ajout de like sur les postes
            for ($j = 0; $j < mt_rand(0, 20); $j++) {
                $like = new BlogLike();
                $like->setUser($faker->randomElement($users))
                    ->setPost($faker->randomElement($posts));
                $manager->persist($like);
            }
        }

        $manager->flush();
    }
}
