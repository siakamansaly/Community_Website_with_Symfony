<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserPreferencesFormType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
    public function userPreferences(User $user, Request $request, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($user);


        $form = $this->createForm(UserPreferencesFormType::class,$user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                //$user->setToken($token);
                //$userRepository->add($user);
                // Add message Flash and redirect to home
                //$this->addFlash('success', "A password reset link has been sent, please consult your email address.");
                //return $this->redirectToRoute('index');
        }

        return $this->render('security/user_preferences.html.twig', [
            'userPreferencesForm' => $form->createView(),
        ]);
    }
}
