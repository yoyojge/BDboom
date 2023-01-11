<?php

namespace App\Controller\Admin;

use App\Entity\Collectionn;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CollectionnCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Collectionn::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
