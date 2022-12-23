<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\String\Slugger\SluggerInterface;

class PostFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly SluggerInterface $slugger
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // use the factory to create a faker\generator instance
        $faker = Faker\Factory::create('en_US');

        for ($post_i = 1; $post_i <= 10; ++$post_i) {
            $post = new Post();

            $post->setTitle($faker->text(15));
            $post->computeSlug($this->slugger, $post->getTitle());
            $post->setPublishedAt(
                $faker->boolean(75)
                    ? \DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-50 days', '10 days'))
                    : null
            );
            $post->setBody($faker->paragraph(10));

            // search user reference
            /** @var User $admin */
            $admin = $this->getReference('admin');
            $post->setAuthor($admin);

            $tagNumber = $faker->numberBetween(1, 3);
            for ($tag_i = 1; $tag_i <= $tagNumber; ++$tag_i)
            {
                /** @var Tag $tag */
                $tag = $this->getReference('tag_'.$tag_i);
                $post->addTag($tag);
            }


            $this->setReference('post_'.$post_i, $post);
            $manager->persist($post);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            TagFixtures::class
        ];
    }
}
