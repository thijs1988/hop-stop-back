<?php

namespace App\Controller\Admin;

use App\Entity\Products;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProductsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Products::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name'),
            TextField::new('type'),
            IntegerField::new('price'),
            TextField::new('description'),
            IntegerField::new('inventory')->setRequired(true),
            AssociationField::new('brands')->setRequired(true),
            CollectionField::new('categories')->hideOnForm(),
            AssociationField::new('categories')->onlyOnForms()->setRequired(true),
            AssociationField::new('images')->onlyOnForms()->setRequired(true),
            CollectionField::new('images')
                ->setTemplatePath('images.html.twig')
                ->onlyOnDetail(),
            AssociationField::new('logo')->onlyOnForms(),
            TextField::new('ingredients'),
            TextField::new('country'),
            TextField::new('content'),
            IntegerField::new('ibu'),
            NumberField::new('abv'),
            BooleanField::new('offer'),
            BooleanField::new('featured'),
            BooleanField::new('comboDeal'),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->add(CRUD::PAGE_INDEX, 'detail');
    }
}
