<?php

namespace App\EventListener;

use App\Entity\CartItems;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

class AuthenticationSuccessListener
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof User) {
            return;
        }

        $data['id'] = $user->getId();
        if($user->getCartItems()){
            $cartItems = $user->getCartItems()->getId();
            $data['cartId'] = $cartItems;
        }else{
            $cartItems = new CartItems();
            $cartItems->setCartOwner($user);
            $this->entityManager->persist($cartItems);
            $user->setCartItems($cartItems);
            $this->entityManager->flush();
            $data['cartId'] = $cartItems->getId();
        }

        $event->setData($data);
    }
}
