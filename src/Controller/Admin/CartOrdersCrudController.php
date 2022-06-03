<?php


namespace App\Controller\Admin;


use App\Entity\Transactions;
use App\Form\ShippedType;
use App\Repository\TransactionsRepository;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CartOrdersCrudController extends  CartProductsCrudController
{
    private $transactionsRepository;

    public function __construct(TransactionsRepository $transactionsRepository)
    {
        $this->transactionsRepository = $transactionsRepository;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setPageTitle(Crud::PAGE_INDEX, 'Unprocessed Cart Orders');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('cart'),
            NumberField::new('amount'),
            AssociationField::new('product')
        ];
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $ids = $this->getOrdersToShow();
        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->andWhere('entity.cart IN (:cart)')
            ->setParameter('cart', $ids);
    }

    public function getOrdersToShow(){
        $cartIds = [];
        $transactions = $this->transactionsRepository->findByCartId();

        foreach($transactions as $item){
            $cartIds[] = $item->getCartId();
        }

        return $cartIds;
    }


}