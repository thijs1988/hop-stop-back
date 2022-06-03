<?php

namespace App\Controller\Admin;

use App\Entity\Coupons;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CouponsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Coupons::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('coupon'),
            TextField::new('title'),
            TextField::new('purpose'),
            BooleanField::new('exclusive'),
            DateTimeField::new('expireDate'),
            ChoiceField::new('discountType')->setChoices(["percent" => "percent", "euro" => "euro"]),
            IntegerField::new('value'),
            BooleanField::new('valid')
        ];
    }
}
