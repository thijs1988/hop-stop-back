<?php

namespace App\Controller\Admin;

use App\Entity\Transactions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TransactionsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Transactions::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('orderId')->hideOnForm(),
            DateField::new('createdAt'),
            IntegerField::new('cartId'),
            TextField::new('name'),
            EmailField::new('email'),
            TextField::new('place'),
            TextField::new('postbox'),
            TextField::new('street'),
            TextField::new('country'),
            NumberField::new('amount'),
            TextField::new('status'),
            TextField::new('phoneNumber'),
            TextField::new('items'),
            BooleanField::new('shipped'),
            IntegerField::new('coupon')
        ];
    }
}
