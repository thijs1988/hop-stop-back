<?php


namespace App\Mailer;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class OrderMailer
{
    private $mailer;
    private $twig;

    public function __construct(MailerInterface $mailer, Environment $twig )
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function sendOrderConfirmationEmail($transaction, $coupon, $cartItems, $cartSession)
    {
        $email = (new TemplatedEmail())
            ->from('info@hop-stop.nl')
            ->to( new Address($transaction->getEmail()), 'hopstop.bier@gmail.com')
            ->subject('Uw bestelling op Hop-Stop')
            ->textTemplate('email/orderConfirmation.txt.twig')
            ->htmlTemplate('email/orderConfirmation.html.twig')
            ->context([
                'transaction' => $transaction,
                'coupon' => $coupon,
                'cartItems' => $cartItems,
                'cartSession' => $cartSession
            ]);

        $this->mailer->send($email);
    }
}