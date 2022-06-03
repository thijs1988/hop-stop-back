<?php


namespace App\Controller;


use App\Mailer\OrderMailer;
use App\Repository\CartProductsRepository;
use App\Repository\CouponsRepository;
use App\Repository\ProductsRepository;
use App\Repository\TransactionsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class MollieController extends AbstractController
{
    private $entityManager;
    private $transactionsRepository;
    private $mailer;
    private $twig;
    private $productsRepository;
    private $cartProductsRepository;
    private $couponsRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        TransactionsRepository $transactionsRepository,
        OrderMailer $mailer,
        Environment $twig,
        ProductsRepository $productsRepository,
        CartProductsRepository $cartProductsRepository,
        CouponsRepository $couponsRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->transactionsRepository = $transactionsRepository;
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->productsRepository = $productsRepository;
        $this->cartProductsRepository = $cartProductsRepository;
        $this->couponsRepository = $couponsRepository;
    }

    /**
     * @throws \Mollie\Api\Exceptions\ApiException
     * @Route("/pay/{orderId}", name="pay")
     */
    public function createPayment($orderId, $apiPublic){
        $transaction = $this->transactionsRepository->findOneBy(['orderId' => $orderId]);
        $amount = number_format($transaction->getAmount() / 100, 2);

        $mollie = new \Mollie\Api\MollieApiClient();
        $mollie->setApiKey($apiPublic);

        try {
            /*
             * Generate a unique order id for this example. It is important to include this unique attribute
             * in the redirectUrl (below) so a proper return page can be shown to the customer.
             */

            /*
             * Payment parameters:
             *   amount        Amount in EUROs. This example creates a € 10,- payment.
             *   description   Description of the payment.
             *   redirectUrl   Redirect location. The customer will be redirected there after the payment.
             *   webhookUrl    Webhook location, used to report when the payment changes state.
             *   metadata      Custom metadata that is stored with the payment.
             */
            $payment = $mollie->payments->create([
                "amount" => [
                    "currency" => "EUR",
                    "value" => $amount,
                ],
                "description" => "Order #{$orderId}",
                "redirectUrl" => "http://clean-taxis-bathe-86-83-1-241.loca.lt/paymentStatus/".$orderId,
                "webhookUrl" => "http://c2d5-2a02-a46b-46de-1-e44a-53ab-fcb7-913.ngrok.io/webhook",
                "metadata" => [
                    "order_id" => $orderId,
                ],
            ]);

            // store the order with its payment status in a database.
            $transaction->setStatus($payment->status);

            $this->entityManager->flush();

            // Send the customer off to complete the payment.
            return new RedirectResponse($payment->getCheckoutUrl(), 303);
        } catch (\Mollie\Api\Exceptions\ApiException $e) {
            return new Response("API call failed: " . htmlspecialchars($e->getMessage()));
        }
    }

    /**
     * @Route("/webhook", name="webhook")
     */
    public function webhookUrl($apiPublic){
        try {
//           initialize
            $mollie = new \Mollie\Api\MollieApiClient();
            $mollie->setApiKey($apiPublic);

            // Retrieve the payment's current state.

            $payment = $mollie->payments->get($_POST["id"]);
            $orderId = $payment->metadata->order_id;
            $transaction = $this->transactionsRepository->findOneBy(['orderId' => $orderId]);


            // Update the order in the database.
            $transaction->setStatus($payment->status);
            $this->entityManager->flush();

            if ($payment->isPaid() && ! $payment->hasRefunds() && ! $payment->hasChargebacks()) {
                if($transaction && $transaction->getCartId() === 0){
                    $cartSession = json_decode($transaction->getItems(), true);
                    foreach ($cartSession as $product){
                        $dbProduct = $this->productsRepository->findOneBy(['id' => substr($product['productId'], 14, strlen($product['productId']))]);
                        if ($dbProduct->getInventory() && $dbProduct->getInventory() >= $product['amount'][0]){
                            $dbProduct->setInventory($dbProduct->getInventory() - $product['amount'][0]);
                        }elseif($dbProduct->getInventory() && $dbProduct->getInventory() > 0 && $dbProduct->getInventory() < $product['amount'][0]){
                            $dbProduct->setInventory(0);
                        }
                        $this->entityManager->flush();
                    }
                }

                if($transaction && $transaction->getCartId() !== 0){
                    $cartItems = $this->cartProductsRepository->findBy(['cart' => $transaction->getCartId()]);
                    foreach ($cartItems as $cartProduct){
                        $dbProduct = $this->productsRepository->findOneBy(['id' => $cartProduct->getProduct()->getId()]);
                        if ($dbProduct->getInventory() && $dbProduct->getInventory() >= $cartProduct->getAmount()){
                            $dbProduct->setInventory($dbProduct->getInventory() - $cartProduct->getAmount());
                        }elseif($dbProduct->getInventory() && $dbProduct->getInventory() > 0 && $dbProduct->getInventory() < $cartProduct->getAmount()){
                            $dbProduct->setInventory(0);
                        }
                        $this->entityManager->flush();
                    }
                }

                if($transaction->getCoupon()){
                    $coupon = $this->couponsRepository->findOneBy(['id' => $transaction->getCoupon()]);
                    if ($coupon && $coupon->getExclusive() === 1){
                       $coupon->setValid(0);
                       $this->entityManager->flush();
                    }
                }

                $coupon = $this->couponsRepository->findOneBy(['id' => $transaction->getCoupon()]);
                $cartItems = $this->cartProductsRepository->findBy(['cart' => $transaction->getCartId()]);
                $cartSession = json_decode($transaction->getItems(), true);


                $this->mailer->sendOrderConfirmationEmail($transaction, $coupon, $cartItems, $cartSession);
                return new Response('it worked');
            } elseif ($payment->isFailed()) {
            } elseif ($payment->isExpired()) {
            } elseif ($payment->isCanceled()) {
            } elseif ($payment->hasRefunds()) {
            } elseif ($payment->hasChargebacks()) {
            }
            return new Response('it failed');
        } catch (\Mollie\Api\Exceptions\ApiException $e) {
            return new Response("API call failed: " . htmlspecialchars($e->getMessage()));
        }
    }

    /**
     * @Route("/confirmation", name="confirmation")
     */
    public function confirmation(){
        return new Response(
            '<html><body>gelukt</body></html>'
        );
    }


}