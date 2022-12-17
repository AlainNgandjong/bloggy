<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class TagFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('en_US');


        for($tag_i = 1; $tag_i <  $faker->numberBetween(1,3) ; $tag_i++) {
            $tag = new Tag();

            $tag->setName($faker->word());

            /** @var Post $post */
            $post = $this->getReference('post_'.$faker->numberBetween(1,3));

            $tag->addPost($post);
            $manager->persist($tag);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            PostFixtures::class
        ];
    }
}
