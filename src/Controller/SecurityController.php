<?php

namespace App\Controller;

use App\Form\ForgotPasswordFormType;
use App\Form\ResetPasswordFormType;
use App\Repository\UserRepository;
use App\Service\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // If user is already logged in, redirect to homepage
        if ($this->getUser()) {
            return $this->redirectToRoute('index');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
    }

    /**
     * @Route("/forgot_password", name="app_forgot_password")
     */
    public function forgotPassword(Request $request, UserRepository $userRepository, Mailer $mailer): Response
    {
        $form = $this->createForm(ForgotPasswordFormType::class);
        // Create Forgot Password Form
        $form->handleRequest($request);
        $adminEmail = $this->getParameter('default_admin_email');

        // When Forgot Password Form is submitted
        if ($form->isSubmitted() && $form->isValid()) {
            // Verify if user exists
            $user = $userRepository->findOneBy(['email' => $form->get('email')->getData()]);

            // If user exists
            if ($user) {
                // Generate token
                $token = $user->generateToken();
                $user->setToken($token);
                $userRepository->add($user);
                // create Login Link
                $loginLinkDetails = $this->generateUrl('app_reset_password', ['token' => $token]);
                // prepare and send email
                $context = [
                    'to' => $user->getEmail(),
                    'from' => $adminEmail,
                    'token' => $loginLinkDetails,
                    'subject' => 'SnowTricks - Forgot password link',
                    'content' => "<p>Hi,</p><p>To change password of your SnowTricks user account, please click on the following link :</p>",
                    'template' => "email/registration.html.twig"
                ];
                $mailer->sendEmailTemplate($context);

                // Add message Flash and redirect to home
                $this->addFlash('success', "A password reset link has been sent, please consult your email address.");
                return $this->redirectToRoute('app_forgot_password');
            }
            // Add message Flash and redirect to home
            $this->addFlash('danger', "This email address not exist.");
        }
        return $this->render('security/forgot_password.html.twig', ['forgotPasswordForm' => $form->createView(),]);
    }

    /**
     * @Route("/reset_password", name="app_reset_password")
     */
    public function resetPassword(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        // If get token
        if ($request->get('token')) {
            // Get user by token
            $user = $userRepository->findOneBy(['token' => $request->get('token')]);

            // If user exists
            if ($user) {
                // Create Reset Password Form
                $form = $this->createForm(ResetPasswordFormType::class);
                $form->handleRequest($request);

                // When Reset Password Form is submitted
                if ($form->isSubmitted() && $form->isValid()) {

                    // If email is verified
                    if ($user->getEmail() === $form->get('email')->getData()) {
                        // Hash password
                        $user->setPassword($userPasswordHasher->hashPassword($user, $form->get('password')->getData()));
                        // Remove token
                        $user->setToken(null);
                        $user->setTokenDate(null);
                        // Activate user
                        $user->setStatus(1);
                        $userRepository->add($user);
                        $this->addFlash('success', 'Password successfully changed! You can now connect !');
                        return $this->redirectToRoute('index');
                    }
                    $this->addFlash('danger', 'This email is not assigned to this token');
                }
                return $this->render('security/reset_password.html.twig', ['resetPasswordForm' => $form->createView(),]);
            }
            $this->addFlash('danger', 'Invalid or expired token !');
        }
        return $this->redirectToRoute('index');
    }
}
