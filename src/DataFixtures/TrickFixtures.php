<?php

namespace App\DataFixtures;

use App\Entity\Trick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\DataFixtures\TypeTrickFixtures;
use App\DataFixtures\UserFixtures;
use App\Entity\TypeTrick;
use App\Entity\User;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class TrickFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager): void
    {
        date_default_timezone_set('Europe/Paris');
        $faker = \Faker\Factory::create();
        $typeTrick = $manager->getRepository(TypeTrick::class);
        $typeTrick = $typeTrick->findAll();
        $user = $manager->getRepository(User::class);
        $user = $user->findOneBy(['email' => 'admin@example.fr']);
        $slugId = 1;
        foreach ($typeTrick as $value) {
            for ($i = 0; $i < rand(0, 8); $i++) {
                $trick = new Trick();
                $trick->setContent($faker->paragraphs(4, true))
                    ->setCreatedAt($faker->dateTimeBetween('-30 days','now'))
                    ->setFeaturedPicture("https://picsum.photos/seed/" . $faker->words(1, true) . "/1280/720")
                    ->setSlug($slugId . "-" . $faker->slug(3, false))
                    ->setTitle($faker->words(3, true))
                    ->setType($value)
                    ->setUser($user);
                $manager->persist($trick);
                $slugId++;
            }
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            TypeTrickFixtures::class,
            UserFixtures::class,
        ];
    }
}
