<?php

namespace App\DataFixtures;

use App\Entity\Categories;
use App\Entity\User;
use App\Factory\CartItemsFactory;
use App\Factory\CartProductsFactory;
use App\Factory\CategoriesFactory;
use App\Factory\ProductsFactory;
use App\Factory\UserFactory;
use App\Repository\CategoriesRepository;
use App\Security\TokenGenerator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use function Zenstruck\Foundry\create;

class AppFixtures extends Fixture
{
    private $entityManager;
    private $passwordEncoder;
    private $tokenGenerator;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder, TokenGenerator $tokenGenerator)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenGenerator = $tokenGenerator;
    }

    public function load(ObjectManager $manager): void
    {
        $categories = CategoriesFactory::createMany(5);

        $subCategories = CategoriesFactory::createMany(20, function() use ($categories) {
            return [
              'parent' => $categories[array_rand($categories)]
            ];
        });

        $subCategoriesLevel2 = CategoriesFactory::createMany(30, function () use ($subCategories){
           return [
             'parent' => $subCategories[array_rand($subCategories)]
           ];
        });

        $products = ProductsFactory::createMany(50, function() use ($subCategoriesLevel2) {
            return [
                'categories' => [$subCategoriesLevel2[array_rand($subCategoriesLevel2)]]
            ];
        });

        $products2 = ProductsFactory::createMany(50, function() use ($subCategories) {
            return [
                'categories' => [$subCategories[array_rand($subCategories)]]
            ];
        });

        $user = new User();
        $user->setUsername('thijs');
        $user->setEmail('thijsdw1@gamil.com');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setAge(34);
        $user->setPhoneNumber('06-12873056');
        $user->setPostbox('5122HB');
        $user->setPlace('Rijen');
        $user->setStreet('Frederikplein 8');
        $user->setName('thijs');
        $user->setEnabled(false);
        $user->setConfirmationToken($this->tokenGenerator->getRandomSecureToken());
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'Test123'));
        $manager->persist($user);

//        $prodArr = [];
//        for ($i = 0; $i <= 5; $i++){
//            $prodArr[] = $products[array_rand($products)];
//        }

        $cart = CartItemsFactory::new()->create(function() use ($user) {
            return[
              'cartOwner' => $user,
            ];
        });


        CartProductsFactory::createMany(5, function() use($products, $cart){
           return [
             'cart' =>  $cart,
             'product' => $products[array_rand($products)]
           ];
        });

        $user2 = new User();
        $user2->setUsername('thijs2');
        $user2->setEmail('thij@gamil.com');
        $user2->setRoles(['ROLE_USER']);
        $user2->setAge(34);
        $user2->setPhoneNumber('06-12873056');
        $user2->setPostbox('5122HB');
        $user2->setPlace('Rijen');
        $user2->setStreet('Frederikplein 8');
        $user2->setName('thijs');
        $user2->setEnabled(true);
        $user2->setPassword($this->passwordEncoder->encodePassword($user2, 'Test123'));
        $manager->persist($user2);

        $cart2 = CartItemsFactory::new()->create(function() use ($user2) {
            return[
                'cartOwner' => $user2,
            ];
        });


        CartProductsFactory::createMany(5, function() use($products, $cart2){
            return [
                'cart' =>  $cart2,
                'product' => $products[array_rand($products)]
            ];
        });

        $manager->flush();
    }
}
