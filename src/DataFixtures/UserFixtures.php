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
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($user);
        $manager->flush();
    }
}
