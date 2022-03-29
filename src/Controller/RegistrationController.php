<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Mailer;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */

    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserRepository $userRepository, Mailer $mailer): Response
    {
        date_default_timezone_set($this->getParameter('app.timezone'));
        $adminEmail = $this->getParameter('default_admin_email');
        $user = new User();

        // Create Registration Form
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        // When Registration Form is submitted
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            // set Status, Registration Date, Role and token user
            $user->setStatus(0);
            $user->setRegistrationDate(new DateTime('now'));
            $user->setRoles(['ROLE_USER']);
            $token = $user->generateToken();
            $user->setToken($token);
            $user->setTokenDate(new DateTime('now'));
            $userRepository->add($user);

            // create Login Link
            $loginLinkDetails = $this->generateUrl('activation', ['token' => $token]);

            // prepare and send email
            $context = [
                'to' => $user->getEmail(),
                'from' => $adminEmail,
                'token' => $loginLinkDetails,
                'subject' => 'SnowTricks - Account creation validation link',
                'content' => "<p>Hi,</p><p>To validate the creation of your SnowTricks user account, please click on the following link :</p>",
                'template' => "email/registration.html.twig"
            ];
            $mailer->sendEmailTemplate($context);

            // Add message Flash and redirect to home
            $this->addFlash('success', "To finalize your registration, please consult your email address.");
            return $this->redirectToRoute('index');
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/activation", name="activation")
     */
    public function activation(Request $request, UserRepository $userRepository): Response
    {
        // If there are get token from url
        if ($request->get('token')) {
            // Find user by token
            $user = $userRepository->findOneBy(['token' => $request->get('token')]);
            // If user exist
            if ($user) {
                // Set user status to 1 and remove token
                $user->setStatus(1);
                $user->setToken(null);
                $user->setTokenDate(null);
                $userRepository->add($user);
                $this->addFlash('success', 'Account activated successfully ! You can log in!');
                return $this->redirectToRoute('index');
            }
            $this->addFlash('danger', 'Invalid or expired token !');
        }
        return $this->redirectToRoute('index');
    }
}
