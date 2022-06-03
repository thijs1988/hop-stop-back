<?php


namespace App\DataPersister;


use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\CartProducts;
use App\Repository\CartProductsRepository;
use Doctrine\ORM\EntityManagerInterface;

class CartProductsDataPersister implements ContextAwareDataPersisterInterface
{

    private $decoratedDataPersister;
    private $entityManager;
    private $cartProductsRepository;

    public function __construct(DataPersisterInterface $decoratedDataPersister, EntityManagerInterface $entityManager, CartProductsRepository $cartProductsRepository)
    {
        $this->decoratedDataPersister = $decoratedDataPersister;
        $this->entityManager = $entityManager;
        $this->cartProductsRepository = $cartProductsRepository;
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof CartProducts;
    }

    public function persist($data, array $context = [])
    {
        if (($context['collection_operation_name'] ?? null) === 'post') {
            if ($cartItems = $this->cartProductsRepository->findBy(['cart' => $data->getCart(), 'product' => $data->getProduct()])) {
                $total = 0;
                foreach ($cartItems as $item) {
                    $total += $item->getAmount();
                    $this->entityManager->remove($item);
                }
                $data->setAmount($total + $data->getAmount());
                $this->entityManager->persist($data);
                $this->entityManager->flush();
            } else {
                $this->entityManager->persist($data);
                $this->entityManager->flush();
            }
        }else{
            $this->entityManager->persist($data);
            $this->entityManager->flush();
        }
    }

    public function remove($data, array $context = [])
    {
        $this->entityManager->remove($data);
        $this->entityManager->flush();
    }
}