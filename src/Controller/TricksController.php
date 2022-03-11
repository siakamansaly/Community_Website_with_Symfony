<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Trick;
use App\Repository\TrickRepository;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use App\Repository\TypeTrickRepository;

class TricksController  extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(EntityManagerInterface $manager)
    {
        $tricks = $manager->getRepository(Trick::class);
        $tricksAll = $tricks->findAll();

        return $this->render('home/index.html.twig', ['tricks' => $tricksAll]);
    }
}
