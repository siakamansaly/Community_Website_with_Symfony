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
        $video = [
            1 => [
                'name' => 'https://www.youtube.com/embed/YAABnJfKJ5w'
            ],
            2 => [
                'name' => 'https://www.youtube.com/embed/G9qlTInKbNE'
            ],
            3 => [
                'name' => 'https://www.youtube.com/embed/397Z2HrHn-4'
            ],
            4 => [
                'name' => 'https://www.youtube.com/embed/JMS2PGAFMcE'
            ],
            5 => [
                'name' => 'https://www.youtube.com/embed/GnYAlEt-s00'
            ],
            6 => [
                'name' => 'https://www.youtube.com/embed/XyARvRQhGgk'
            ],
            7 => [
                'name' => 'https://www.youtube.com/embed/nMAvJtpNvJI'
            ],
            8 => [
                'name' => 'https://www.youtube.com/embed/s3jRiFyOijw'
            ],
            9 => [
                'name' => 'https://www.youtube.com/embed/6nt12SR2kX0'
            ],
            10 => [
                'name' => 'https://www.youtube.com/embed/6gFsbU3GWF0'
            ]
        ];
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
                $video_random =  $video[rand(1, 10)]['name'];
                $media->setName($video_random)
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
