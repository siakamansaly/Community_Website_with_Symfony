<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create();
        $user = new User();
        $password = $this->hasher->hashPassword($user, 'password');
        $user->setFirstname($faker->firstName())
            ->setLastname($faker->lastName())
            ->setEmail('admin@example.fr')
            ->setPassword($password)
            ->setRegistrationDate($faker->dateTime())
            ->setStatus(1)
            ->setPicture('https://i.pravatar.cc/300?u=a042581f4e29026704a')
            ->setRoles(['ROLE_ADMIN']);
        $manager->persist($user);
        for ($i = 0; $i < rand(3, 15); $i++) {
            $user = new User();
            $password = $this->hasher->hashPassword($user, 'password');
            $user->setFirstname($faker->firstName())
                ->setLastname($faker->lastName())
                ->setEmail($faker->email())
                ->setPassword($password)
                ->setRegistrationDate($faker->dateTime())
                ->setStatus(1)
                ->setPicture('https://i.pravatar.cc/300?img='.$i)
                ->setRoles(['ROLE_USER']);
            $manager->persist($user);
        }
        $manager->flush();
    }
}
