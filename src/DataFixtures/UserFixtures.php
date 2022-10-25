<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher) {
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('camile@camile.fr')
            ->setUsername('camile')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->hasher->hashPassword($user, 'camile'))
            ;
        $manager->persist($user);

        $faker = Faker\Factory::create('fr_FR');


        for($i = 0; $i <10; $i++) {
            $user = new User();
            $user->setEmail($faker->email())
                ->setUsername($faker->userName())
                ->setPassword($this->hasher->hashPassword($user, 'password'))
            ;
            $manager->persist($user);
        }
        $manager->flush();
    }
}
