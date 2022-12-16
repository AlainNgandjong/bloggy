<?php

namespace App\Controller\Admin;

use App\Entity\Tag;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TagCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Tag::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [

            yield TextField::new('name'),
            yield SlugField::new('slug')
                ->setTargetFieldname('name')
                ->setFormTypeOption(
                    'disabled',
                    $pageName !== Crud::PAGE_NEW
                )

        ];
    }

}
