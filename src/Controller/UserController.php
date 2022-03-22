<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\DeleteTrickFormType;
use App\Form\UserProfileFormType;
use App\Form\UserRoleFormType;
use App\Repository\TrickRepository;
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
        return $this->render('user/index.html.twig', ['users' => $users->findAll()]);
    }

    /**
     * @Route("/admin/user/{id}/edit_role", name="app_user_role")
     */
    public function adminUserRole(User $user, Request $request, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserRoleFormType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $userRepository->add($user);
            $message = " The role of ".$user->getFirstname()." ".$user->getLastname()." has changed successfully !!";
            $this->addFlash('success',$message);
            return $this->redirectToRoute('app_users');
        }
        
        return $this->render('user/role.html.twig', ['userRoleForm' => $form->createView()]);
    }

    /**
     * @Route("/admin/user/{id}/delete", name="app_user_delete")
     */
    public function adminUserDelete(User $user, Request $request, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($user);

        if($request->request->count()>0) {

            $userRepository->remove($user);
            $message = " The account of ".$user->getFirstname()." ".$user->getLastname()." has deleted successfully !!";
            $this->addFlash('success',$message);
            return $this->redirectToRoute('app_users');
        }
        
        return $this->render('user/delete.html.twig', ['user' => $user]);
    }

    /**
     * @Route("/profile/user/{id}/edit", name="app_user_edit")
     */
    public function profileUser(User $user, Request $request, UserRepository $userRepository, TrickRepository $trickRepository, FileUploader $fileUploader, UrlComposer $urlComposer, TricksController $tricksController): Response
    {
        $user = $userRepository->find($user);
        $pictureTemp = "";
        //
        if($this->isGranted('ROLE_ADMIN')==false && $this->getUser()->getUserIdentifier()!=$user->getEmail())
        {
            return $this->redirectToRoute('index');
        }
        $oldPicture = $user->getPicture();
        $form = $this->createForm(UserProfileFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //$user->setToken($token);

            $picture = $form->get('picture')->getData();
            if ($picture) {
                $pictureFileName = $fileUploader->upload($picture,'profile');
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

        // Delete Trick Form
        $formDeleteTrick = $this->createForm(DeleteTrickFormType::class);
        $formDeleteTrick->handleRequest($request);

        if ($formDeleteTrick->isSubmitted() && $formDeleteTrick->isValid()) {
            $trick = $trickRepository->find($formDeleteTrick->get('delete')->getData());
            $tricksController->deleteTrick($trick,$trickRepository);
            return $this->redirectToRoute('app_user_edit', ['id' => $user->getId()]);
        }

        $pictureTemp = $urlComposer->url('profile',$user->getPicture());
        $page = "false";
        if($request->get('d')){
            $page = "true";
        }
        return $this->render('user/profile.html.twig', ['userPreferencesForm' => $form->createView(), 'picture' => $pictureTemp, 'page' => $page, 'user' =>$user, 'deleteForm' => $formDeleteTrick->createView()]);
    }
}
