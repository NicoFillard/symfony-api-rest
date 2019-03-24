<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Author;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->encoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $categories = [];
        $articles = [];

        // Category fixtures
        for ($i = 0; $i < 10; $i++) {
            $category = new Category();
            $category->setName('Category' . $i );
            $manager->persist($category);
            $categories[] = $category;

        }

        // Article fixtures and Author fixtures
        for ($i = 0; $i < 10; $i++) {
            $article = new Article();
            $article->setName('Article' . $i );
            $article->setDescription('Lorem ipsum kotlin c\'est bien Symfony c\'est mieux');
            $article->setCategory($categories[rand(0, 9)]);
            $manager->persist($article);
            $articles[] = $article;
            $author = new Author();
            $author->setFirstname('Firstname' . $i );
            $author->setLastname('Lastname' . $i );
            $author->setArticle($article);
            $manager->persist($author);

        }

        // Comment fixtures
        for ($i = 0; $i < 10; $i++) {
            $comment = new Comment();
            $comment->setContent('Quand t\'es dans les six mètres il faut marquer !');
            $comment->setArticle($articles[rand(0, 9)]);
            $manager->persist($comment);
            $categories[] = $comment;

        }

        $user = new User();
        $user->setEmail('fillard.nico@hotmail.fr');
        $user->setRoles([
            'ROLE_ADMIN'
        ]);
        $user->setPassword($this->encoder->encodePassword($user, 'kotlinforever'));
        $manager->persist($user);

        $manager->flush();
    }
}
