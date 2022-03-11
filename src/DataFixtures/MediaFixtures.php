<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Trick;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\DataFixtures\TrickFixtures;
use App\Entity\Media;

class MediaFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create();
        $trick = $manager->getRepository(Trick::class);
        $trick = $trick->findAll();

        foreach ($trick as $key => $value) {
            for ($i = 0; $i < rand(0, 5); $i++) {
                $media = new Media();
                $media->setName($faker->imageUrl(408, 200, $value->getTitle(), true))
                ->setTrick($value)
                ->setType('TYPE_IMAGE');
                $manager->persist($media);
            }

            for ($j = 0; $j < rand(0, 5); $j++) {
                $media = new Media();
                $media->setName($faker->url())
                ->setTrick($value)
                ->setType('TYPE_VIDEO');
                $manager->persist($media);
            }
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            TrickFixtures::class,
        ];
    }
}
