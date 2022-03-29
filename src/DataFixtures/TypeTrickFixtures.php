<?php

namespace App\DataFixtures;

use App\Entity\TypeTrick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TypeTrickFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = [
            1 => [
                'name' => 'Straight airs',
                'description' => ""
            ],
            2 => [
                'name' => 'Grabs',
                'description' => ""
            ],
            3 => [
                'name' => 'Spins',
                'description' => ""
            ],
            4 => [
                'name' => 'Flips and inverted rotations',
                'description' => ""
            ],
            5 => [
                'name' => 'Inverted hand plants',
                'description' => ""
            ],
            6 => [
                'name' => 'Stalls',
                'description' => ""
            ],
            7 => [
                'name' => 'Tweaks and variations',
                'description' => ""
            ],
            8 => [
                'name' => 'Miscellaneous tricks and identifiers',
                'description' => ""
            ]
        ];
        foreach ($categories as $value) {
            $categorie = new TypeTrick();
            $categorie->setName($value['name']);
            $categorie->setDescription($value['description']);
            $manager->persist($categorie);
        }
        $manager->flush();
    }
}
