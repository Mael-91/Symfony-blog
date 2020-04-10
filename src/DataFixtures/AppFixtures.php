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
                ->setEnabled(true)
                ->setAvatarFilename('avatar.png')
                ->setBannerFilename('test_banner.jpg');
            $manager->persist($user);
            $users[] = $user;
        }

        // Création d'utilisateur connecté via un service oauth
        for ($i = 0; $i < 2; $i++) {
            $user = new User();
            $user->setUsername($faker->userName)
                ->setEmail($faker->email)
                ->setPassword($this->encoder->encodePassword($user, $faker->password))
                ->setFirstName($faker->firstName)
                ->setLastName($faker->lastName)
                ->setSexe(rand(1, 2))
                ->setRoles(['ROLE_USER'])
                ->setEnabled(true)
                ->setOauth(true)
                ->setAvatarFilename('avatar.png')
                ->setBannerFilename('test_banner.jpg');
            $manager->persist($user);
            $users[] = $user;
        }

        // Création d'utilisateur non vérifié
        for ($i = 0; $i < 2; $i++) {
            $user = new User();
            $user->setUsername($faker->userName)
                ->setEmail($faker->email)
                ->setPassword($this->encoder->encodePassword($user, $faker->password))
                ->setFirstName($faker->firstName)
                ->setLastName($faker->lastName)
                ->setSexe(rand(1, 2))
                ->setRoles(['ROLE_USER'])
                ->setEnabled(true)
                ->setEnabled(false)
                ->setAvatarFilename('avatar.png')
                ->setBannerFilename('test_banner.jpg');
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
        for ($i = 0; $i < 10; $i++) {
            $post = new Blog();
            $post->setTitle($faker->sentence)
                ->setContent($faker->paragraph(15))
                ->setAuthor($faker->randomElement($users))
                ->setPictureFilename('5e7caf4628a43110309176.jpg')
                ->setBannerFilename('5e7cb6cc40cda350914190.jpg')
                ->setCategory($faker->randomElement($categories));
            $manager->persist($post);
            $posts[] = $post;

            // Création des commentaires parents aléatoire
            for ($c = 0; $c < 5; $c++) {
                $comment = new BlogComment();
                $comment->setPost($faker->randomElement($posts))
                    ->setAuthor($faker->randomElement($users))
                    ->setContent($faker->paragraph);
                $manager->persist($comment);
                $parents[] = $comment;
            }

            // Création des commentaires enfants
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
