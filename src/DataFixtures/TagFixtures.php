<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class TagFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for($i = 0; $i <10; $i++) {
            $tag = new Tag();
            $tag->setname($faker->word())
            ;
            $tag->setTask($this->getReference('task' . rand(0, 4)));

            $manager->persist($tag);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            TaskFixtures::class,
        ];
    }
}
