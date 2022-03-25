<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use Faker;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;


class PostFixtures extends Fixture
{

//    public function __construct(
//        private SluggerInterface $slugger
//    ){}

    public function load(ObjectManager $manager): void
    {
        // use the factory to create a faker\generator instance
        $faker = Faker\Factory::create('en_US');

        for($post_i = 1; $post_i <= 10 ; $post_i++)
        {
            $post = new Post();

            $post->setTitle($faker->title());
            $post->setSlug($post->getTitle());
            $post->setBody($faker->text());
            $post->setPublishedAt(new \DateTimeImmutable('now'));

            // search user reference
            /** @var User $admin */
            $admin = $this->getReference('admin');

            $post->setAuthor($admin);

//            $this->setReference('post-'.$post_i, $post);
            $manager->persist($post);
        }

        $manager->flush();
    }
}
