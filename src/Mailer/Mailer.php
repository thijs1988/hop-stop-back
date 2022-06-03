<?php


namespace App\Mailer;


use App\Entity\User;
use Symfony\Bridge\Twig\Mime\BodyRenderer;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Mailer
{
    private $mailer;
    private $twig;

    public function __construct(MailerInterface $mailer, Environment $twig )
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function sendConfirmationEmail(User $user)
    {
        $email = (new TemplatedEmail())
            ->from('info@hop-stop.nl')
            ->to(new Address($user->getEmail()))
            ->subject('Please confirm your account')
            ->htmlTemplate('email/confirmation.html.twig')
            ->textTemplate('email/confirmation.txt.twig')
            ->context([
                'user' => $user
            ]);

        $this->mailer->send($email);
    }
}