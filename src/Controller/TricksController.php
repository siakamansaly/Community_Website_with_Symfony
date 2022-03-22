<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\MediaPicture;
use App\Entity\MediaVideo;
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
use App\Repository\CommentRepository;
use App\Repository\MediaPictureRepository;
use App\Repository\MediaVideoRepository;
use App\Repository\TrickRepository;
use App\Service\FileUploader;
use App\Service\UrlComposer;
use DateTimeImmutable;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\SluggerInterface;

class TricksController  extends AbstractController
{
    private string $removePicture;
    /**
     * @Route("/", name="index")
     */
    public function index(TrickRepository $trickRepository, Request $request)
    {
        $tricksAll = $trickRepository->findBy([], ['createdAt' => 'DESC']);
        // Delete Trick Form
        $formDeleteTrick = $this->createForm(DeleteTrickFormType::class);
        $formDeleteTrick->handleRequest($request);

        if ($formDeleteTrick->isSubmitted() && $formDeleteTrick->isValid()) {
            $trick = $trickRepository->find($formDeleteTrick->get('delete')->getData());
            $this->deleteTrick($trick,$trickRepository);
            return $this->redirectToRoute('index');
        }
        return $this->render('home/index.html.twig', ['tricks' => $tricksAll, 'deleteForm' => $formDeleteTrick->createView()]);
    }

    /**
     * @Route("/trick/{slug}", name="app_show_trick")
     */
    public function trickShow(Trick $trick, Request $request, CommentRepository $commentRepository, UrlComposer $urlComposer): Response
    {
        date_default_timezone_set($this->getParameter('app.timezone'));
        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new DateTimeImmutable('now'));
            $comment->setTrick($trick);
            $comment->setUser($this->getUser());
            $commentRepository->add($comment);
            $this->addFlash('success', 'Comment successfully added !!');
            return $this->redirectToRoute('app_show_trick', ['slug' => $trick->getSlug(), '_fragment' => 'trick-0']);
        }

        $FeaturedPicture = $urlComposer->url('tricks', $trick->getFeaturedPicture());
        if (!$FeaturedPicture) {
            $FeaturedPicture = '/SnowTricks.png';
        }
        $pictures = [];
        $pictures = $urlComposer->urlArray('tricks', $trick->getMediasPicture());


        return $this->render('single/index.html.twig', ['trick' => $trick, 'commentForm' => $form->createView(), 'FeaturedPicture' => $FeaturedPicture, 'pictures' => $pictures]);
    }

    /**
     * @Route("/profile/trick/add", name="app_add_trick")
     */
    public function addTrickPage(Request $request, TrickRepository $trickRepository, SluggerInterface $slugger, FileUploader $fileUploader, MediaVideoRepository $mediaVideoRepository, MediaPictureRepository $mediaPictureRepository, UrlComposer $urlComposer)
    {
        $trick = new Trick();
        $form = $this->createForm(TrickFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // If the featured image is not empty, upload the image
            $featuredPicture = $form->get('featuredPicture')->getData();
            if ($featuredPicture) {
                $featuredPictureFileName = $fileUploader->upload($featuredPicture, 'tricks');
                $trick->setFeaturedPicture($featuredPictureFileName);
            }

            $trick->setCreatedAt(new \DateTime('now'))
                ->setSlug(uniqid())
                ->setUser($this->getUser())
                ->setTitle($form->get('title')->getData())
                ->setContent($form->get('content')->getData())
                ->setType($form->get('type')->getData());
            $trickRepository->add($trick);
            $trick->setSlug($trick->getId() . '-' . $slugger->slug($form->get('title')->getData()));

            $mediasVideos = $form->get('mediasVideos')->getData();
            if ($mediasVideos) {
                foreach ($mediasVideos as $key => $value) {
                    $video = new MediaVideo();
                    $video->setName($urlComposer->urlEmbed($value->getName()));
                    $video->setTrick($trick);
                    $mediaVideoRepository->add($video);
                }
            }

            $mediasPicture = $form->get('mediasPicture')->getData();
            if ($mediasPicture) {
                foreach ($mediasPicture as $key => $value) {
                    $picture = new MediaPicture();
                    $upload = new UploadedFile($value->getName(), uniqid());
                    $pictureFileName = $fileUploader->upload($upload, 'tricks');
                    $picture->setName($pictureFileName);
                    $picture->setTrick($trick);
                    $mediaPictureRepository->add($picture);
                }
            }

            $trickRepository->add($trick);
            $this->addFlash('success', 'The trick has added successfully !!');
            return $this->redirectToRoute('index');
        }

        return $this->render('trick/add.html.twig', ['trickForm' => $form->createView()]);
    }

    /**
     * @Route("/profile/trick/{id}/edit", name="app_edit_trick")
     */
    public function editTrickPage(Trick $trick, Request $request, TrickRepository $trickRepository, SluggerInterface $slugger, FileUploader $fileUploader, UrlComposer $urlComposer, MediaPictureRepository $mediaPictureRepo, MediaVideoRepository $mediaVideoRepo): Response
    {
        date_default_timezone_set($this->getParameter('app.timezone'));

        // Get old featured picture
        $removePicture = $trick->getFeaturedPicture();

        // Get all old pictures
        foreach ($trick->getMediasPicture() as $key => $value) {
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

        if ($formDeleteTrick->isSubmitted() && $formDeleteTrick->isValid()) {
            $this->deleteTrick($trick,$trickRepository);
            return $this->redirectToRoute('index');
        }

        // When content Form is submitted
        if ($form->isSubmitted() && $form->isValid()) {
            $trick->setUpdatedTo(new \DateTime('now'))
                ->setSlug($trick->getId() . '-' . $slugger->slug($form->get('title')->getData()))
                ->setTitle($form->get('title')->getData())
                ->setContent($form->get('content')->getData())
                ->setType($form->get('type')->getData());
            $trickRepository->add($trick);

            $this->addFlash('success', 'Trick successfully updated !!');
            return $this->redirectToRoute('app_edit_trick', ['id' => $trick->getId()]);
        }
        // When Featured picture Form is submitted
        if ($formFeatured->isSubmitted()) {
            if ($formFeatured->isValid()) {
                $featuredPicture = $formFeatured->get('featuredPicture')->getData();
                if ($featuredPicture) {
                    $featuredPictureFileName = $fileUploader->upload($featuredPicture, 'tricks');
                    if ($removePicture) {
                        $filesystemEdit = new Filesystem();
                        $filesystemEdit->remove($this->getParameter('tricks_directory') . '/' . $removePicture);
                    }
                    $trick->setFeaturedPicture($featuredPictureFileName);
                    $trickRepository->add($trick);
                    $this->addFlash('success', 'Featured picture successfully updated !!');
                    return $this->redirectToRoute('app_edit_trick', ['id' => $trick->getId()]);
                }
            }
            $this->addFlash('danger', 'Featured picture error upload !!');
        }

        // When other picture Form is submitted
        if ($formPictures->isSubmitted()) {
            if ($formPictures->isValid()) {
                $otherPicture = $formPictures->get('name')->getData();
                $otherPictureFileName = $fileUploader->upload($otherPicture, 'tricks');
                switch ($formPictures->get('pictureEdit')->getData()) {
                    case -1:
                        $mediaPicture = new MediaPicture();
                        $mediaPicture->setName($otherPictureFileName);
                        $mediaPicture->setTrick($trick);
                        break;

                    default:
                        if ($removeOtherPictures[$formPictures->get('pictureEdit')->getData()]) {
                            $filesystemEdit = new Filesystem();
                            $filesystemEdit->remove($this->getParameter('tricks_directory') . '/' . $removeOtherPictures[$formPictures->get('pictureEdit')->getData()]);
                        }
                        $mediaPicture = $mediaPictureRepo->find($formPictures->get('pictureEdit')->getData());
                        $mediaPicture->setName($otherPictureFileName);
                        $this->addFlash('success', 'The picture successfully updated !!');
                        break;
                }
                $mediaPictureRepo->add($mediaPicture);
                return $this->redirectToRoute('app_edit_trick', ['id' => $trick->getId()]);
            }
            $this->addFlash('danger', 'Picture error upload !!');
        }

        // When video Form is submitted
        if ($formVideos->isSubmitted()) {
            if ($formVideos->isValid()) {
                $videoLink = $urlComposer->urlEmbed($formVideos->get('name')->getData());
                switch ($formVideos->get('videoEdit')->getData()) {
                    case -1:
                        $mediaVideo = new MediaVideo();
                        $mediaVideo->setName($videoLink);
                        $mediaVideo->setTrick($trick);
                        $this->addFlash('success', 'The video successfully added !!');
                        break;

                    default:
                        $mediaVideo = $mediaVideoRepo->find($formVideos->get('videoEdit')->getData());
                        $mediaVideo->setName($videoLink);
                        $this->addFlash('success', 'The video successfully updated !!');
                        break;
                }
                $mediaVideoRepo->add($mediaVideo);
                return $this->redirectToRoute('app_edit_trick', ['id' => $trick->getId()]);
            }
            $this->addFlash('danger', 'Video error link !!');
        }

        // Generate URL Featured Picture 
        $FeaturedPicture = $urlComposer->url('tricks', $trick->getFeaturedPicture());
        if (!$FeaturedPicture) {
            $FeaturedPicture = '/SnowTricks.png';
        }

        // Generate URL other pictures 
        $pictures = [];
        $pictures = $urlComposer->urlArray('tricks', $trick->getMediasPicture());

        return $this->render('trick/edit.html.twig', ['trick' => $trick, 'trickForm' => $form->createView(), 'FeaturedPicture' => $FeaturedPicture, 'pictures' => $pictures, 'featuredForm' => $formFeatured->createView(), 'picturesForm' => $formPictures->createView(), 'videosForm' => $formVideos->createView(), 'deleteForm' => $formDeleteTrick->createView()]);
    }


    public function deleteTrick(Trick $trick, TrickRepository $trickRepository): bool
    {
            $trickRepository->remove($trick);
            $this->addFlash('success',"The trick has deleted successfully !!");
            return true;
    }
}
