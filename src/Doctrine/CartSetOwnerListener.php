<?php


namespace App\Doctrine;


use App\Entity\CartItems;
use Symfony\Component\Security\Core\Security;

class CartSetOwnerListener
{
    private $security;

    public function __construct(Security $security){
        $this->security = $security;
    }

    public function prePersist(CartItems $cartItems){
        if ($cartItems->getCartOwner()){
            return;
        }

        if($this->security->getUser()){
            $cartItems->setCartOwner($this->security->getUser());
        }
    }
}