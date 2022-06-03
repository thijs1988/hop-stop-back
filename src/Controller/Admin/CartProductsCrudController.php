<?php

namespace App\Controller\Admin;

use App\Entity\CartProducts;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class CartProductsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CartProducts::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            IntegerField::new('amount'),
            IntegerField::new('price'),
            AssociationField::new('cart'),
            AssociationField::new('product'),
        ];
    }
}
