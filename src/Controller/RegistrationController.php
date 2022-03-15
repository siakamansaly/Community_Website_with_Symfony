<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UserPreferencesFormType;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Mailer;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class RegistrationController extends AbstractController
{

    /**
     * @Route("/register", name="app_register")
     */

    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, Mailer $mailer, FlashBagInterface $flashBagInterface): Response
    {
        date_default_timezone_set($this->getParameter('app.timezone'));
        $adminEmail = $this->getParameter('default_admin_email');
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            // set Status, Registration Date and Role user
            $user->setStatus(0);
            $user->setRegistrationDate(new DateTime('now'));
            $user->setRoles(['ROLE_USER']);
            $token = $user->generateToken();
            $user->setToken($token);
            $user->setTokenDate(new DateTime('now'));

            $entityManager->persist($user);
            $entityManager->flush();

            // create Login Link
            $loginLinkDetails = $this->generateUrl('activation',['token' => $token]);

            // prepare and send email
            $context['to'] = $user->getEmail();
            $context['from'] = $adminEmail;
            $context['token'] = $loginLinkDetails;
            $context['subject'] = 'SnowTricks - Account creation validation link';
            $context['content'] = "<p>Hi,</p>
            <p>To validate the creation of your SnowTricks user account, please click on the following link :</p>";
            $context['template'] = "email/registration.html.twig";
            $mailer->sendEmailTemplate($context);

            // Add message Flash and redirect to home
            $flashBagInterface->add('success', "To finalize your registration, please consult your email address.");
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
        if ($request->get('token')) {
            $user = $userRepository->findOneBy(['token' => $request->get('token')]);
            if ($user) {
                $user->setStatus(1);
                $user->setToken(NULL);
                $user->setTokenDate(NULL);
                $userRepository->add($user);
                $this->addFlash('success', 'Account activated successfully ! You can log in!');
                return $this->redirectToRoute('index');
            }
            $this->addFlash('danger', 'Invalid or expired token !');
        }
        return $this->redirectToRoute('index');
    }

    
}
