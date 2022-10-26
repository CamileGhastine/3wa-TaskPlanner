<?php

namespace App\DataFixtures;

use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class TaskFixtures extends Fixture implements DependentFixtureInterface
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
                ->setUser($this->getReference('user' . rand(0,9)))
                ;
            for($j = 0; $j < rand(1, 3); $j++) {
                $task->addCategory($this->getReference('category' . rand(0, 4)));
            }

            $this->addReference('task' . $i, $task);

            $manager->persist($task);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            CategoryFixtures::class,
        ];
    }
}
