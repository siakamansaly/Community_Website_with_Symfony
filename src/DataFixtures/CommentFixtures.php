<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Trick;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\DataFixtures\TrickFixtures;
use App\DataFixtures\UserFixtures;
use App\Entity\Comment;
use App\Entity\User;
use DateTimeImmutable;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        date_default_timezone_set('Europe/Paris');
        $faker = \Faker\Factory::create();
        $trick = $manager->getRepository(Trick::class);
        $trick = $trick->findAll();
        $users = $manager->getRepository(User::class);
        $users = $users->findAll();


        foreach ($trick as $key => $value) {
            foreach ($users as $key => $user) {
                for ($i = 0; $i < rand(0, 2); $i++) {
                    $comment = new Comment();
                    $comment->setContent($faker->paragraphs(2, true))
                        ->setCreatedAt(new \DateTimeImmutable('now'))
                        ->setTrick($value)
                        ->setUser($user);
                    $manager->persist($comment);
                }
            }
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            TrickFixtures::class,
            UserFixtures::class,
        ];
    }
}
