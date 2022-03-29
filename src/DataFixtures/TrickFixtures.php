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
use Symfony\Component\String\Slugger\SluggerInterface;

class TrickFixtures extends Fixture implements DependentFixtureInterface
{
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        date_default_timezone_set('Europe/Paris');
        $faker = \Faker\Factory::create();
        $typeTrick = $manager->getRepository(TypeTrick::class);
        $user = $manager->getRepository(User::class);
        $user = $user->findOneBy(['email' => 'admin@example.fr']);
        $slugId = 1;
        $tricks = [
            1 => [
                'title' => 'Ollie',
                'content' => "A trick in which the snowboarder springs off the tail of the board and into the air",
                'featured_picture' => 'https://images.unsplash.com/photo-1625154869776-100eba31abbb?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1791&q=80',
                'type_trick' => 'Straight airs',
            ],
            2 => [
                'title' => 'Shifty',
                'content' => "An aerial trick in which a snowboarder counter-rotates their upper body in order to shift their board about 90° from its normal position beneath them, and then returns the board to its original position before landing. This trick can be performed frontside or backside, and also in variation with other tricks and spins.",
                'featured_picture' => 'https://images.unsplash.com/photo-1553815035-92b6eef66226?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=2070&q=80',
                'type_trick' => 'Straight airs',
            ],
            3 => [
                'title' => 'One-Two',
                'content' => "A trick in which the rider's front hand grabs the heel edge behind their back foot.",
                'featured_picture' => '',
                'type_trick' => 'Grabs',
            ],
            4 => [
                'title' => 'Chicken salad',
                'content' => 'The rear hand reaches between the legs and grabs the heel edge between the bindings while the front leg is boned. The wrist is rotated inward to complete the grab.',
                'featured_picture' => 'https://images.unsplash.com/photo-1617939533073-6c94c709370c?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=2072&q=80',
                'type_trick' => 'Grabs',
            ],
            5 => [
                'title' => 'Back flip',
                'content' => 'Flipping backward off of a jump, the rider\'s back foot is placed in the air and the front foot is placed on the ground. The rider\'s front hand grabs the heel edge behind their back foot.',
                'featured_picture' => 'https://images.unsplash.com/photo-1609165230172-0421afd42bf1?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1170&q=80',
                'type_trick' => 'Flips and inverted rotations',
            ],
            6 => [
                'title' => 'Frontside Rodeo',
                'content' => 'The basic frontside rodeo is performed as a 540. It essentially falls into a grey area between an off-axis frontside 540 and a frontside 180 with a backflip blended into it. The grab choice and different line and pop factors can make it more flippy or more of an off-axis spin. Frontside rodeo can be done off the heels or toes and with a little more spin on the horizontal axis can go to 720 or 900. The bigger the horizontal spin, the later the inverted part of the rotation should be. Gaining control on big spin rodeos may lead to a double cork or a second flip rotation in the spin, if the rider has developed a comfort level with double flips on a trampoline or other gymnastic environment.;Rodeo flip; frontside rodeo: A frontward-flipping frontside spin done off the toe edge. Most commonly performed with a 540° rotation, but also performed as a 720°, 900°, etc..',
                'featured_picture' => 'https://images.unsplash.com/photo-1600765282502-2ccd9543f0d7?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1170&q=80',
                'type_trick' => 'Flips and inverted rotations',
            ],
            7 => [
                'title' => 'J-Tear',
                'content' => 'Inverted frontside 540 with a hand plant in the middle. Originally a variation on the Jacoby Terror Air. This trick was invented by Mike Jacoby for a contest that didn\'t allow inverted aerials; inverted handplants, however, were acceptable.',
                'featured_picture' => 'https://images.unsplash.com/photo-1518085050105-3c33befa5442?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1170&q=80',
                'type_trick' => 'Inverted hand plants',
            ],
            8 => [
                'title' => 'Tweak',
                'content' => 'A term used in western ski areas for when a trick is highly refined in movement, such as with legs or arms fully extended, to give maximum aesthetic quality to a trick. Demonstrates high technical ability, much like in gymnastics.',
                'featured_picture' => 'https://images.unsplash.com/photo-1621362159273-c44a4baa1938?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1170&q=80',
                'type_trick' => 'Tweaks and variations',
            ],
            9 => [
                'title' => 'Tail-stall',
                'content' => 'The opposite of a nose-stall, this trick involves stalling on an obstacle with the tail of the snowboard. Often performed by approaching an obstacle fakie or by doing a 180 after approaching the feature normally',
                'featured_picture' => 'https://images.unsplash.com/photo-1522445263200-1b4b91053db8?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=2070&q=80',
                'type_trick' => 'Stalls',
            ],
            10 => [
                'title' => 'Pretzel',
                'content' => 'Concluding a slide trick with a 270° spin opposite the direction in which you did a rotation during the trick\'s initiation.',
                'featured_picture' => 'https://images.unsplash.com/photo-1504480899134-8d1752cc1162?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1869&q=80',
                'type_trick' => 'Miscellaneous tricks and identifiers',
            ]
        ];
        foreach ($tricks as $trickValue) {
            $typeTrick = $manager->getRepository(TypeTrick::class);
            $typeTrick = $typeTrick->findOneBy(['name' => $trickValue['type_trick']]);
            $trick = new Trick();
            $trick->setContent($trickValue['content'])
                    ->setCreatedAt($faker->dateTimeBetween('-30 days', 'now'))
                    ->setFeaturedPicture($trickValue['featured_picture'] ? $trickValue['featured_picture'] : null)
                    ->setSlug($this->slugger->slug($slugId . "-" . $trickValue['title']))
                    ->setTitle($trickValue['title'])
                    ->setType($typeTrick)
                    ->setUser($user);
            $manager->persist($trick);
            $slugId++;
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
