<?php

namespace App\Controller;

use App\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Trick;
use App\Form\CommentFormType;
use App\Repository\CommentRepository;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    public function trickShow(Trick $trick, Request $request, CommentRepository $commentRepository) : Response
    {
        date_default_timezone_set($this->getParameter('app.timezone'));
        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $comment->setCreatedAt(new DateTimeImmutable('now'));
            $comment->setTrick($trick);
            $comment->setUser($this->getUser());
            $commentRepository->add($comment);
            $this->addFlash('success','Comment successfully added !!');
            return $this->redirectToRoute('trickShow',['id'=>$trick->getId()]);
        }

        return $this->render('single/index.html.twig', ['trick' => $trick, 'commentForm' => $form->createView()]);
    }
}
