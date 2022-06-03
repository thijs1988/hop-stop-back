<?php

namespace App\Controller\Admin;

use App\Entity\CartItems;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CartItemsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CartItems::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            DateField::new('expireDate')->hideOnForm(),
            DateField::new('createdAt')->hideOnForm(),
            BooleanField::new('paid'),
            BooleanField::new('shipped'),
            AssociationField::new('cartOwner')->autocomplete(),
        ];
    }

}
