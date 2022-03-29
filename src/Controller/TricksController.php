<?php

namespace App\Controller;

use App\Entity\MediaPicture;
use App\Entity\MediaVideo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Trick;
use App\Repository\MediaPictureRepository;
use App\Repository\MediaVideoRepository;
use App\Repository\TrickRepository;
use App\Service\FileUploader;
use App\Service\UrlComposer;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\SluggerInterface;

class TricksController extends AbstractController
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
     * Add a new trick
     * @param Trick $trick
     */
    public function addTrick($form, Trick $trick)
    {
        // If the featured image is not empty, upload the image
        $featuredPicture = $form->get('featuredPicture')->getData();
        if ($featuredPicture) {
            $ImageFileName = $this->fileUploader->upload($featuredPicture, 'tricks');
            $trick->setFeaturedPicture($ImageFileName);
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
     * Update a trick content
     * @param Trick $trick
     */
    public function updateTrickContent($form, Trick $trick)
    {
        $this->denyAccessUnlessGranted('TRICK_EDIT', $trick);
        $trick->setUpdatedTo(new \DateTime('now'))
            ->setSlug($trick->getId() . '-' . $this->slugger->slug($form->get('title')->getData()))
            ->setTitle($form->get('title')->getData())
            ->setContent($form->get('content')->getData())
            ->setType($form->get('type')->getData());
        $this->trickRepository->add($trick);
        $this->addFlash('success', 'Trick successfully updated !!');
    }

    /**
     * Update a trick Featured Picture
     * @param Trick $trick
     */
    public function updateTrickFeatured($formFeatured, Trick $trick, $removePicture)
    {
        $this->denyAccessUnlessGranted('TRICK_EDIT', $trick);
        $featuredPicture = $formFeatured->get('featuredPicture')->getData();
        // If the featured image is not empty, upload the image
        if ($featuredPicture) {
            $ImageFileName = $this->fileUploader->upload($featuredPicture, 'tricks');
            // If the old featured image is not empty, remove the old picture
            if ($removePicture) {
                $filesystemEdit = new Filesystem();
                $filesystemEdit->remove($this->getParameter('tricks_directory') . '/' . $removePicture);
            }
            $trick->setFeaturedPicture($ImageFileName);
            $this->trickRepository->add($trick);
            $this->addFlash('success', 'Featured picture successfully updated !!');
        }
    }

    /**
     * Update a trick medias picture
     * @param Trick $trick
     */
    public function updateTrickPicture($formPictures, Trick $trick, $removeOtherPictures)
    {
        $this->denyAccessUnlessGranted('TRICK_EDIT', $trick);
        $otherPicture = $formPictures->get('name')->getData();
        // Upload the new picture
        $otherPictureFileName = $this->fileUploader->upload($otherPicture, 'tricks');

        // If picture like -1, add the new picture else remove the old picture and add the new one
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

    /**
     * Update a trick medias video
     * @param Trick $trick
     */
    public function updateTrickVideo($formVideos, Trick $trick)
    {
        $this->denyAccessUnlessGranted('TRICK_EDIT', $trick);
        // Formated the video url
        $videoLink = $this->urlComposer->urlEmbed($formVideos->get('name')->getData());

        // If video like -1, add the new video else remove the old video and add the new one
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

    /**
     * Delete a trick
     */
    public function deleteTrick(int $idTrick, string $action): Response
    {
        // Switch the action, delete featured picture, delete pictures, delete videos or delete the trick
        switch ($action) {
            case 'featured':
                $trick = $this->trickRepository->find($idTrick);
                if ($trick->getFeaturedPicture()) {
                    $filesystemEdit = new Filesystem();
                    $filesystemEdit->remove($this->getParameter('tricks_directory') . '/' . $trick->getFeaturedPicture());
                }
                $id_redirect = $trick->getId();
                $trick->setFeaturedPicture(null);
                $this->trickRepository->add($trick);
                $this->addFlash('success', 'Featured picture successfully deleted !!');
                return $this->redirectToRoute('app_edit_trick', ['id' => $trick->getId()]);
                break;
            case 'picture':
                $mediaPicture = $this->mediaPictureRepo->find($idTrick);
                if ($mediaPicture->getName()) {
                    $filesystemEdit = new Filesystem();
                    $filesystemEdit->remove($this->getParameter('tricks_directory') . '/' . $mediaPicture->getName());
                }
                $id_redirect = $mediaPicture->getTrick()->getId();
                $this->mediaPictureRepo->remove($mediaPicture);
                $this->addFlash('success', 'Picture successfully deleted !!');
                return $this->redirectToRoute('app_edit_trick', ['id' => $id_redirect]);
                break;
            case 'video':
                $mediaVideo = $this->mediaVideoRepo->find($idTrick);
                $id_redirect = $mediaVideo->getTrick()->getId();
                $this->mediaVideoRepo->remove($mediaVideo);
                $this->addFlash('success', 'Video successfully deleted !!');
                return $this->redirectToRoute('app_edit_trick', ['id' => $id_redirect]);
                break;
            case 'trick':
                $trick = $this->trickRepository->find($idTrick);
                if ($trick->getFeaturedPicture()) {
                    $filesystemEdit = new Filesystem();
                    $filesystemEdit->remove($this->getParameter('tricks_directory') . '/' . $trick->getFeaturedPicture());
                }
                $medias = $this->mediaPictureRepo->findBy(['trick' => $trick]);
                foreach ($medias as $media) {
                    $filesystemEdit = new Filesystem();
                    $filesystemEdit->remove($this->getParameter('tricks_directory') . '/' . $media->getName());
                }
                $this->trickRepository->remove($trick);
                $this->addFlash('success', "The trick has deleted successfully !!");
                return $this->redirectToRoute('index');
                break;
        }
        $this->addFlash('danger', "An error has occurred !!");
        return $this->redirectToRoute('index');
    }
}
