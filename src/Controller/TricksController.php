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
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\SluggerInterface;

class TricksController  extends AbstractController
{
    private $trickRepository;
    private $mediaPictureRepo;
    private $mediaVideoRepo;
    private $urlComposer;
    private $slugger;
    private $fileUploader;

    public function __construct(TrickRepository $trickRepository, MediaPictureRepository $mediaPictureRepo, MediaVideoRepository $mediaVideoRepo, UrlComposer $urlComposer, SluggerInterface $slugger, FileUploader $fileUploader)
    {
        $this->trickRepository = $trickRepository;
        $this->mediaPictureRepo = $mediaPictureRepo;
        $this->mediaVideoRepo = $mediaVideoRepo;
        $this->urlComposer = $urlComposer;
        $this->slugger = $slugger;
        $this->fileUploader = $fileUploader;
    }
    /**
     * @Route("/", name="index")
     */
    public function index(Request $request)
    {
        $tricksAll = $this->trickRepository->findBy([], ['createdAt' => 'DESC']);
        // Delete Trick Form
        $formDeleteTrick = $this->createForm(DeleteTrickFormType::class);
        $formDeleteTrick->handleRequest($request);

        if ($formDeleteTrick->isSubmitted() && $formDeleteTrick->isValid()) {
            $trick = $this->trickRepository->find($formDeleteTrick->get('delete')->getData());
            $this->deleteTrick($trick->getId(), 'trick');
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

        return $this->render('single/index.html.twig', ['trick' => $trick, 'commentForm' => $form->createView(), 'FeaturedPicture' => $FeaturedPicture, 'pictures' => $pictures]);
    }

    /**
     * @Route("/profile/trick/add", name="app_add_trick")
     */
    public function addTrickPage(Request $request)
    {
        $trick = new Trick();
        $form = $this->createForm(TrickFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->addTrick($form, $trick);
            return $this->redirectToRoute('index');
        }

        return $this->render('trick/add.html.twig', ['trickForm' => $form->createView()]);
    }

    public function addTrick($form, Trick $trick)
    {
        // If the featured image is not empty, upload the image
        $featuredPicture = $form->get('featuredPicture')->getData();
        if ($featuredPicture) {
            $featuredPictureFileName = $this->fileUploader->upload($featuredPicture, 'tricks');
            $trick->setFeaturedPicture($featuredPictureFileName);
        }

        $trick->setCreatedAt(new \DateTime('now'))
            ->setSlug(uniqid())
            ->setUser($this->getUser())
            ->setTitle($form->get('title')->getData())
            ->setContent($form->get('content')->getData())
            ->setType($form->get('type')->getData());
        $this->trickRepository->add($trick);
        $trick->setSlug($trick->getId() . '-' . $this->slugger->slug($form->get('title')->getData()));

        $mediasVideos = $form->get('mediasVideos')->getData();
        if ($mediasVideos) {
            foreach ($mediasVideos as $key => $value) {
                $video = new MediaVideo();
                $video->setName($this->urlComposer->urlEmbed($value->getName()));
                $video->setTrick($trick);
                $this->mediaVideoRepo->add($video);
            }
        }

        $mediasPicture = $form->get('mediasPicture')->getData();
        if ($mediasPicture) {
            foreach ($mediasPicture as $key => $value) {
                $picture = new MediaPicture();
                $upload = new UploadedFile($value->getName(), uniqid());
                $pictureFileName = $this->fileUploader->upload($upload, 'tricks');
                $picture->setName($pictureFileName);
                $picture->setTrick($trick);
                $this->mediaPictureRepo->add($picture);
            }
        }

        $this->trickRepository->add($trick);
        $this->addFlash('success', 'The trick has added successfully !!');
    }

    /**
     * @Route("/profile/trick/{id}/edit", name="app_edit_trick")
     */
    public function editTrickPage(Trick $trick, Request $request): Response
    {
        $this->denyAccessUnlessGranted('TRICK_EDIT',$trick);
        date_default_timezone_set($this->getParameter('app.timezone'));

        // Get old featured picture
        $removePicture = [];
        $removePicture = $trick->getFeaturedPicture();

        // Get all old pictures
        $removeOtherPictures = [];
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

        // When delete Trick Form is submitted
        if ($formDeleteTrick->isSubmitted() && $formDeleteTrick->isValid()) {
            $this->deleteTrick($formDeleteTrick->get('delete')->getData(), $formDeleteTrick->get('action')->getData());
        }

        // When content Form is submitted
        if ($form->isSubmitted() && $form->isValid()) {
            $this->updateTrickContent($form, $trick);
            return $this->redirectToRoute('app_edit_trick', ['id' => $trick->getId()]);
        }
        // When Featured picture Form is submitted
        if ($formFeatured->isSubmitted()) {
            if ($formFeatured->isValid()) {
                $this->updateTrickFeatured($formFeatured, $trick, $removePicture);
                return $this->redirectToRoute('app_edit_trick', ['id' => $trick->getId()]);
            }
            $this->addFlash('danger', 'Featured picture error upload !!');
        }

        // When other picture Form is submitted
        if ($formPictures->isSubmitted()) {
            if ($formPictures->isValid()) {
                $this->updateTrickPicture($formPictures, $trick, $removeOtherPictures);
                return $this->redirectToRoute('app_edit_trick', ['id' => $trick->getId()]);
            }
            $this->addFlash('danger', 'Picture error upload !!');
        }

        // When video Form is submitted
        if ($formVideos->isSubmitted()) {
            if ($formVideos->isValid()) {
                $this->updateTrickVideo($formVideos, $trick);
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

        return $this->render('trick/edit.html.twig', ['trick' => $trick, 'trickForm' => $form->createView(), 'FeaturedPicture' => $FeaturedPicture, 'pictures' => $pictures, 'featuredForm' => $formFeatured->createView(), 'picturesForm' => $formPictures->createView(), 'videosForm' => $formVideos->createView(), 'deleteForm' => $formDeleteTrick->createView()]);
    }

    public function updateTrickContent($form, Trick $trick)
    {
        $this->denyAccessUnlessGranted('TRICK_EDIT',$trick);
        $trick->setUpdatedTo(new \DateTime('now'))
            ->setSlug($trick->getId() . '-' . $this->slugger->slug($form->get('title')->getData()))
            ->setTitle($form->get('title')->getData())
            ->setContent($form->get('content')->getData())
            ->setType($form->get('type')->getData());
        $this->trickRepository->add($trick);
        $this->addFlash('success', 'Trick successfully updated !!');
    }

    public function updateTrickFeatured($formFeatured, Trick $trick, $removePicture)
    {
        $this->denyAccessUnlessGranted('TRICK_EDIT',$trick);
        $featuredPicture = $formFeatured->get('featuredPicture')->getData();
        if ($featuredPicture) {
            $featuredPictureFileName = $this->fileUploader->upload($featuredPicture, 'tricks');
            if ($removePicture) {
                $filesystemEdit = new Filesystem();
                $filesystemEdit->remove($this->getParameter('tricks_directory') . '/' . $removePicture);
            }
            $trick->setFeaturedPicture($featuredPictureFileName);
            $this->trickRepository->add($trick);
            $this->addFlash('success', 'Featured picture successfully updated !!');
        }
    }

    public function updateTrickPicture($formPictures, Trick $trick, $removeOtherPictures)
    {
        $this->denyAccessUnlessGranted('TRICK_EDIT',$trick);
        $otherPicture = $formPictures->get('name')->getData();
        $otherPictureFileName = $this->fileUploader->upload($otherPicture, 'tricks');
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
                $mediaPicture = $this->mediaPictureRepo->find($formPictures->get('pictureEdit')->getData());
                $mediaPicture->setName($otherPictureFileName);
                $this->addFlash('success', 'The picture successfully updated !!');
                break;
        }
        $this->mediaPictureRepo->add($mediaPicture);
    }

    public function updateTrickVideo($formVideos, Trick $trick)
    {
        $this->denyAccessUnlessGranted('TRICK_EDIT',$trick);
        $videoLink = $this->urlComposer->urlEmbed($formVideos->get('name')->getData());
        switch ($formVideos->get('videoEdit')->getData()) {
            case -1:
                $mediaVideo = new MediaVideo();
                $mediaVideo->setName($videoLink);
                $mediaVideo->setTrick($trick);
                $this->addFlash('success', 'The video successfully added !!');
                break;

            default:
                $mediaVideo = $this->mediaVideoRepo->find($formVideos->get('videoEdit')->getData());
                $mediaVideo->setName($videoLink);
                $this->addFlash('success', 'The video successfully updated !!');
                break;
        }
        $this->mediaVideoRepo->add($mediaVideo);
    }

    public function deleteTrick(int $id, string $action): Response
    {
        
        $trick = $this->trickRepository->find($id);
        $this->denyAccessUnlessGranted('TRICK_DELETE',$trick);

        switch ($action) {
            case 'featured':
                $id_redirect = $trick->getId();
                $trick->setFeaturedPicture(NULL);
                $this->trickRepository->add($trick);
                $this->addFlash('success', 'Featured picture successfully deleted !!');
                break;
            case 'picture':
                $mediaPicture = $this->mediaPictureRepo->find($id);
                $id_redirect = $mediaPicture->getTrick()->getId();
                $this->mediaPictureRepo->remove($mediaPicture);
                $this->addFlash('success', 'Picture successfully deleted !!');
                break;
            case 'video':
                $mediaVideo = $this->mediaVideoRepo->find($id);
                $id_redirect = $mediaVideo->getTrick()->getId();
                $this->mediaVideoRepo->remove($mediaVideo);
                $this->addFlash('success', 'Video successfully deleted !!');
                break;
            case 'trick':
                $id_redirect = $trick->getId();
                $this->trickRepository->remove($trick);
                $this->addFlash('success', "The trick has deleted successfully !!");
                return $this->redirectToRoute('index');
                break;
        }
        return $this->redirectToRoute('app_edit_trick', ['id' => $id_redirect]);
    }
}
