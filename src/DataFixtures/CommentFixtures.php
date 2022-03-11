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

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create();
        $trick = $manager->getRepository(Trick::class);
        $trick = $trick->findAll();
        $user = $manager->getRepository(User::class);
        $user = $user->findOneBy(['email' => 'admin@example.fr']);

        foreach ($trick as $key => $value) {
            for ($i = 0; $i < rand(0, 10); $i++) {
                $comment = new Comment();
                $now = new \DateTime();
                $interval = $now->diff($value->getCreatedAt());
                $days = $interval->days;
                $minimum = '-' . $days . ' days';
                $comment->setContent($faker->paragraphs(2, true))
                    ->setCreatedAt(new \DateTimeImmutable($minimum))
                    ->setTrick($value)
                    ->setUser($user);
                $manager->persist($comment);
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
