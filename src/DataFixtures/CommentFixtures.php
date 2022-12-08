<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('en_US');


        for($comment_i = 1; $comment_i<= $faker->numberBetween(1,20) ; $comment_i++) {
            $comment = new Comment();

            $comment->setName($faker->name());
            $comment->setEmail($faker->email());
            $comment->setContent($faker->paragraph());
            $comment->setIsActive($faker->boolean(90));

            /** @var Post $post */
            $post = $this->getReference('post_'.$faker->numberBetween(1,3));

            $comment->setPost($post);
            $manager->persist($comment);
        }

        $manager->flush();
    }

    public function getDependencies():array
    {
        return [
            PostFixtures::class
        ];
    }
}
