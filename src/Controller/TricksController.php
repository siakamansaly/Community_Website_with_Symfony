<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Trick;
use Symfony\Component\HttpFoundation\Session\Session;

class TricksController  extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(EntityManagerInterface $manager, Session $session)
    {
        $tricks = $manager->getRepository(Trick::class);
        $tricksAll = $tricks->findAll();
        $session->getFlashBag();

        return $this->render('home/index.html.twig', ['tricks' => $tricksAll, 'flashbag_message'=>$session]);
    }

    /**
     * @Route("/trick/{id}", name="trickShow")
     */
    public function trickShow(Trick $trick)
    {
        return $this->render('single/index.html.twig', ['trick' => $trick]);
    }
}
