<?php

namespace App\Controller;

use App\Mailer\OrderMailer;
use App\Repository\CartProductsRepository;
use App\Repository\CouponsRepository;
use App\Repository\TransactionsRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class OrderEmailController extends AbstractController
{
    /**
     * @Route("/order/email", name="order_email")
     */
    public function index(MailerInterface $mailer): Response
    {
        $email = (new TemplatedEmail())
            ->from('thijsdw1@gmail.com')
            ->to('uhjrtiuhir@g.com')
            ->subject('Uw bestelling op Hop-Stop')
            ->textTemplate('email/orderConfirmation.txt.twig')
            ->htmlTemplate('email/orderConfirmation.html.twig');


        $mailer->send($email);
        return new Response('email send');
    }

//    /**
//     * @Route("/send", name="send")
//     */
//    public function send(
//        OrderMailer $mailer, TransactionsRepository $transactionsRepository, CouponsRepository $couponsRepository, CartProductsRepository $cartProductsRepository
//    ){
//        $transaction = $transactionsRepository->findOneBy(['orderId' => 'fafc83ae-d93a-41a0-8ba4-e84c24e708c8']);
//        $coupon = $couponsRepository->findOneBy(['id' => $transaction->getCoupon()]);
//        $cartItems = $cartProductsRepository->findBy(['cart' => $transaction->getCartId()]);
//        $cartSession = json_decode($transaction->getItems(), true);
//
//        $mailer->sendOrderConfirmationEmail($transaction, $coupon, $cartItems, $cartSession);
//
//        return new Response('worked');
//
//    }
}
