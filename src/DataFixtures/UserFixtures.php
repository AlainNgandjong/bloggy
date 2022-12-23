<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $hasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@bloggy.fr');
        $admin->setName('admin');
        $admin->setPassword($this->hasher->hashPassword($admin, 'admin'));
        $admin->setRoles(['ROLE_ADMIN']);

        $this->setReference('admin', $admin);

        $manager->persist($admin);
        $faker = Faker\Factory::create('en_US');

        for ($user_i = 1; $user_i <= 5; ++$user_i) {
            $user = new User();

            $user->setEmail($faker->email);
            $user->setName($faker->lastName());

            $user->setPassword($this->hasher->hashPassword($user, 'secret'));

            $this->setReference('user-'.$user_i, $user);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
