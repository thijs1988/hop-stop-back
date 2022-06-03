<?php


namespace App\Controller\Admin;


use App\Form\ItemsType;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use phpDocumentor\Reflection\DocBlock\Description;

class SessionOrdersCrudController extends TransactionsCrudController
{
    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setPageTitle(Crud::PAGE_INDEX, 'Transactions waiting to be shipped');

    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('orderId')->hideOnForm(),
            IntegerField::new('cartId'),
            TextField::new('name'),
            EmailField::new('email'),
            TextField::new('place'),
            TextField::new('postbox'),
            BooleanField::new('shipped'),
            TextEditorField::new('items')->setTemplatePath('customFields/text.html.twig')
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(CRUD::PAGE_INDEX, 'detail')
            ->setPermission(Action::EDIT, 'ROLE_SUPER_ADMIN')
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN');

    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->andWhere('entity.status = :paid')
            ->setParameter('paid', 'paid')
            ->andWhere('entity.shipped = :shipped')
            ->setParameter('shipped', 0)
            ->andWhere('entity.cartId = 0');
    }
}