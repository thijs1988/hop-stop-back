<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            EmailField::new('email'),
            ArrayField::new('roles'),
            TextField::new('password')->hideOnForm(),
            TextField::new('plainPassword')->onlyOnForms()->hideWhenUpdating(),
            TextField::new('retypedPassword')->onlyOnForms()->hideWhenUpdating(),
            TextField::new('oldPassword')->onlyOnForms()->hideWhenCreating(),
            TextField::new('newPassword')->onlyOnForms()->hideWhenCreating(),
            TextField::new('retypedPassword')->onlyOnForms()->hideWhenCreating(),
            TextField::new('username'),
            TextField::new('phoneNumber'),
            TextField::new('street'),
            TextField::new('postbox'),
            TextField::new('place'),
            IntegerField::new('age'),
            TextField::new('name'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
        ->setFormOptions(['validation_groups' => ['Default', 'create']], ['validation_groups' => ['Default', 'put_reset_password']]);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->setPermission(Action::NEW, 'ROLE_SUPER_ADMIN')
            ->setPermission(Action::EDIT, 'ROLE_SUPER_ADMIN')
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
            ;
    }

}
