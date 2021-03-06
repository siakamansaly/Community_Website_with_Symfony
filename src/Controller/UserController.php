<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\DeleteTrickFormType;
use App\Form\DeleteUserFormType;
use App\Form\UserProfileFormType;
use App\Form\UserRoleFormType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\FileUploader;
use App\Service\UrlComposer;
use Symfony\Component\Filesystem\Filesystem;

class UserController extends AbstractController
{
    /**
     * @Route("/admin/users", name="app_users")
     */
    public function adminUsers(UserRepository $users): Response
    {
        $this->denyAccessUnlessGranted('USER_DELETE', $this->getUser());
        return $this->render('user/index.html.twig', ['users' => $users->findAll()]);
    }

    /**
     * @Route("/admin/user/{id}/edit_role", name="app_user_role")
     */
    public function adminUserRole(User $user, Request $request, UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('USER_EDIT_ROLE', $this->getUser());

        // Create form for user role
        $form = $this->createForm(UserRoleFormType::class, $user);
        $form->handleRequest($request);

        // If form is submitted, save role
        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->add($user);
            $message = " The role of ".$user->getFirstname()." ".$user->getLastname()." has changed successfully !!";
            $this->addFlash('success', $message);
            return $this->redirectToRoute('app_users');
        }

        return $this->render('user/role.html.twig', ['userRoleForm' => $form->createView()]);
    }

    /**
     * @Route("/admin/user/{id}/delete", name="app_user_delete")
     */
    public function adminUserDelete(User $user, Request $request, UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('USER_DELETE', $this->getUser());

        // Create Delete User form
        $form = $this->createForm(DeleteUserFormType::class, $user);
        $form->handleRequest($request);

        // When delete User Form is submitted
        if ($form->isSubmitted() && $form->isValid()) {
            // If user have a profile picture, delete it
            if ($user->getPicture()) {
                $filesystemEdit = new Filesystem();
                $filesystemEdit->remove($this->getParameter('profiles_directory') . '/' . $user->getPicture());
            }
            // Delete user
            $userRepository->remove($user);
            $message = " The account of ".$user->getFirstname()." ".$user->getLastname()." has deleted successfully !!";
            $this->addFlash('success', $message);
            return $this->redirectToRoute('app_users');
        }

        return $this->render('user/delete.html.twig', ['user' => $user, 'deleteForm' => $form->createView()]);
    }

    /**
     * @Route("/profile/user/{id}/edit", name="app_user_edit")
     */
    public function profileUser(User $user, Request $request, UserRepository $userRepository, FileUploader $fileUploader, UrlComposer $urlComposer, TricksController $tricksController): Response
    {
        $user = $userRepository->find($user);
        $this->denyAccessUnlessGranted('USER_EDIT', $user);
        $pictureTemp = "";

        $oldPicture = $user->getPicture();
        // Create form for user profile
        $form = $this->createForm(UserProfileFormType::class, $user);
        $form->handleRequest($request);

        // When form is submitted, save user
        if ($form->isSubmitted() && $form->isValid()) {
            $picture = $form->get('picture')->getData();
            $user->setPicture($oldPicture);
            // If user have a old profile picture, delete it and upload new one
            if ($picture) {
                $pictureFileName = $fileUploader->upload($picture, 'profile');
                $user->setPicture($pictureFileName);
                if ($oldPicture) {
                    $filesystem = new Filesystem();
                    $filesystem->remove($this->getParameter('profiles_directory') . '/' . $oldPicture);
                }
            }
            $userRepository->add($user);
            $this->addFlash('success', "Update done !");
            $parameters='';
            if ($request->get('d')) {
                $parameters = $request->get('d');
            }
            return $this->redirectToRoute('app_user_edit', ['id' => $user->getId(), 'd'=>$parameters]);
        }

        // Delete Trick Form
        $formDeleteTrick = $this->createForm(DeleteTrickFormType::class);
        $formDeleteTrick->handleRequest($request);

        // When delete Trick Form is submitted
        if ($formDeleteTrick->isSubmitted() && $formDeleteTrick->isValid()) {
            $tricksController->deleteTrick($formDeleteTrick->get('delete')->getData(), 'trick');
            return $this->redirectToRoute('app_user_edit', ['id' => $user->getId()]);
        }

        // Generate url for user profile
        $pictureTemp = $urlComposer->url('profile', $user->getPicture());

        $page = "false";
        if ($request->get('d')) {
            $page = "true";
        }
        return $this->render('user/profile.html.twig', ['userPreferencesForm' => $form->createView(), 'picture' => $pictureTemp, 'page' => $page, 'user' =>$user, 'deleteForm' => $formDeleteTrick->createView()]);
    }
}
