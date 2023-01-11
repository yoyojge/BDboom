<?php

namespace App\Controller\Admin;

use App\Entity\AlbumCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class AlbumCollectionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AlbumCollection::class;
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
