<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;


class Mailer extends AbstractController
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmailTemplate(array $context = []): void
    {
        $email = (new TemplatedEmail())
            ->from($context['from'])
            ->to($context['to'])
            ->subject($context['subject'])
            ->htmlTemplate($context['template'])
            ->context($context);
        $this->mailer->send($email);
    }

}
