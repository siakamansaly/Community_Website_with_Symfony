<?php

namespace App\Controller;

use App\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Trick;
use App\Form\CommentFormType;
use App\Form\DeleteTrickFormType;
use App\Form\FeaturedPictureFormType;
use App\Form\PictureFormType;
use App\Form\TrickEditFormType;
use App\Form\TrickFormType;
use App\Form\VideoFormType;
use App\Repository\TrickRepository;
use App\Service\UrlComposer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Controller\TricksController;
use App\Controller\CommentController;

class DefaultController  extends AbstractController
{
    private $trickRepository;
    private $urlComposer;

    public function __construct(TrickRepository $trickRepository, UrlComposer $urlComposer)
    {
        $this->trickRepository = $trickRepository;
        $this->urlComposer = $urlComposer;
    }
    /**
     * @Route("/", name="index")
     */
    public function index(Request $request, TricksController $tricksController)
    {
        $tricksAll = $this->trickRepository->findBy([], ['createdAt' => 'DESC']);
        // Delete Trick Form
        $formDeleteTrick = $this->createForm(DeleteTrickFormType::class);
        $formDeleteTrick->handleRequest($request);

        if ($formDeleteTrick->isSubmitted() && $formDeleteTrick->isValid()) {
            $trick = $this->trickRepository->find($formDeleteTrick->get('delete')->getData());
            $this->denyAccessUnlessGranted('TRICK_DELETE',$trick);
            $tricksController->deleteTrick($trick->getId(), 'trick');
            return $this->redirectToRoute('index');
        }
        return $this->render('home/index.html.twig', ['tricks' => $tricksAll, 'deleteForm' => $formDeleteTrick->createView()]);
    }

    /**
     * @Route("/trick/{slug}", name="app_show_trick")
     */
    public function trickShow(Trick $trick, Request $request, CommentController $commentController): Response
    {
        date_default_timezone_set($this->getParameter('app.timezone'));
        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $commentController->addComment($comment, $trick);
            return $this->redirectToRoute('app_show_trick', ['slug' => $trick->getSlug(), '_fragment' => 'trick-0']);
        }

        $FeaturedPicture = $this->urlComposer->url('tricks', $trick->getFeaturedPicture());
        if (!$FeaturedPicture) {
            $FeaturedPicture = '/SnowTricks.png';
        }
        $pictures = [];
        $pictures = $this->urlComposer->urlArray('tricks', $trick->getMediasPicture());

        $page = "false";
        if($request->get('d')){
            $page = "true";
        }

        return $this->render('single/index.html.twig', ['trick' => $trick, 'commentForm' => $form->createView(), 'FeaturedPicture' => $FeaturedPicture, 'pictures' => $pictures, 'page' => $page]);
    }

    /**
     * @Route("/profile/trick/add", name="app_add_trick")
     */
    public function addTrickPage(Request $request, TricksController $tricksController)
    {
        $trick = new Trick();
        $form = $this->createForm(TrickFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $tricksController->addTrick($form, $trick);
            return $this->redirectToRoute('index');
        }

        return $this->render('trick/add.html.twig', ['trickForm' => $form->createView()]);
    }

    /**
     * @Route("/profile/trick/{id}/edit", name="app_edit_trick")
     */
    public function editTrickPage(Trick $trick, Request $request, TricksController $tricksController): Response
    {
        $this->denyAccessUnlessGranted('TRICK_EDIT',$trick);
        date_default_timezone_set($this->getParameter('app.timezone'));

        // Get old featured picture
        $removePicture = [];
        $removePicture = $trick->getFeaturedPicture();

        // Get all old pictures
        $removeOtherPictures = [];
        foreach ($trick->getMediasPicture() as $value) {
            $removeOtherPictures[$value->getId()] = $value->getName();
        }

        // Create content Form
        $form = $this->createForm(TrickEditFormType::class, $trick);
        $form->handleRequest($request);

        // Create Featured Picture Form
        $formFeatured = $this->createForm(FeaturedPictureFormType::class, $trick);
        $formFeatured->handleRequest($request);

        // Create other Picture Form
        $formPictures = $this->createForm(PictureFormType::class);
        $formPictures->handleRequest($request);

        // Create video Form
        $formVideos = $this->createForm(VideoFormType::class);
        $formVideos->handleRequest($request);

        // Delete Trick Form
        $formDeleteTrick = $this->createForm(DeleteTrickFormType::class);
        $formDeleteTrick->handleRequest($request);

        // When delete Trick Form is submitted
        if ($formDeleteTrick->isSubmitted() && $formDeleteTrick->isValid()) {
            $this->denyAccessUnlessGranted('TRICK_DELETE',$trick);
            $tricksController->deleteTrick($formDeleteTrick->get('delete')->getData(), $formDeleteTrick->get('action')->getData());
        }

        // When content Form is submitted
        if ($form->isSubmitted() && $form->isValid()) {
            $tricksController->updateTrickContent($form, $trick);
            return $this->redirectToRoute('app_edit_trick', ['id' => $trick->getId()]);
        }
        // When Featured picture Form is submitted
        if ($formFeatured->isSubmitted()) {
            if ($formFeatured->isValid()) {
                $tricksController->updateTrickFeatured($formFeatured, $trick, $removePicture);
                return $this->redirectToRoute('app_edit_trick', ['id' => $trick->getId()]);
            }
            $this->addFlash('danger', 'Featured picture error upload !!');
        }

        // When other picture Form is submitted
        if ($formPictures->isSubmitted()) {
            if ($formPictures->isValid()) {
                $tricksController->updateTrickPicture($formPictures, $trick, $removeOtherPictures);
                return $this->redirectToRoute('app_edit_trick', ['id' => $trick->getId()]);
            }
            $this->addFlash('danger', 'Picture error upload !!');
        }

        // When video Form is submitted
        if ($formVideos->isSubmitted()) {
            if ($formVideos->isValid()) {
                $tricksController->updateTrickVideo($formVideos, $trick);
                return $this->redirectToRoute('app_edit_trick', ['id' => $trick->getId()]);
            }
            $this->addFlash('danger', 'Video error link !!');
        }

        // Generate URL Featured Picture 
        $FeaturedPicture = $this->urlComposer->url('tricks', $trick->getFeaturedPicture());
        if (!$FeaturedPicture) {
            $FeaturedPicture = '/SnowTricks.png';
        }

        // Generate URL other pictures 
        $pictures = [];
        $pictures = $this->urlComposer->urlArray('tricks', $trick->getMediasPicture());
        $page = "false";
        if($request->get('d')){
            $page = "true";
        }

        return $this->render('trick/edit.html.twig', ['trick' => $trick, 'trickForm' => $form->createView(), 'FeaturedPicture' => $FeaturedPicture, 'pictures' => $pictures, 'featuredForm' => $formFeatured->createView(), 'picturesForm' => $formPictures->createView(), 'videosForm' => $formVideos->createView(), 'deleteForm' => $formDeleteTrick->createView(), 'page' => $page]);
    }


}
