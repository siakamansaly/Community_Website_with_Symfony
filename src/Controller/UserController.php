<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserPreferencesFormType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\FileUploader;
use Symfony\Component\Filesystem\Filesystem;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="app_user")
     */
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/user/{id}/edit", name="app_user_edit")
     */
    public function userPreferences(User $user, Request $request, UserRepository $userRepository, FileUploader $fileUploader): Response
    {
        $user = $userRepository->find($user);

        $oldPicture = $user->getPicture();
        $form = $this->createForm(UserPreferencesFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //$user->setToken($token);

            $picture = $form->get('picture')->getData();
            if ($picture) {
                $pictureFileName = $fileUploader->upload($picture);
                $user->setPicture($pictureFileName);
            }
            if ($oldPicture) {
                $filesystem = new Filesystem();
                $filesystem->remove($this->getParameter('profiles_directory') . '/' . $oldPicture);
            }
            $userRepository->add($user);
            // Add message Flash and redirect to home
            $this->addFlash('success', "Update done !");
            return $this->redirectToRoute('app_user_edit', ['id' => $user->getId()]);
        }
        $pictureTemp = $this->getParameter('profiles_directory_path') . '/' . $user->getPicture();
        return $this->render('security/user_preferences.html.twig', ['userPreferencesForm' => $form->createView(), 'picture' => $pictureTemp]);
    }
}
