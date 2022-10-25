<?php

namespace App\DataFixtures;

use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class TaskFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($i = 0; $i < 5; $i++) {
            $date =$faker->dateTimeBetween('-7 days', '+7 days');

            $task = new Task();
            $task->setTitle($faker->sentence())
                ->setDescription($faker->paragraphs(30, true))
                ->setIsDone(false)
                ->setExpiratedAt($date)
                ;

            $manager->persist($task);
        }


        $manager->flush();
    }
}
