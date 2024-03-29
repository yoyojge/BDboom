<?php

namespace App\Controller\Admin;

use App\Entity\AlbumWishlist;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class AlbumWishlistCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AlbumWishlist::class;
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
