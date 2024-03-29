<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Album;
use App\Entity\Wishlist;
use App\Entity\Collectionn;
use App\Entity\AlbumWishlist;
use App\Entity\AlbumCollection;


use App\Controller\Admin\UserCrudController;
use App\Controller\Admin\AlbumCrudController;
use App\Controller\Admin\CollectionnCrudController;
use App\Controller\Admin\WishlistCrudController;
use App\Controller\Admin\AlbumCollectionCrudController;





use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('BDboom');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Collectionneurs', 'fa-solid fa-utensils', User::class);
        yield MenuItem::linkToCrud('Collectionns', 'fa-solid fa-utensils', Collectionn::class);
        yield MenuItem::linkToCrud('Wishlist', 'fa-solid fa-utensils', Wishlist::class);
        yield MenuItem::linkToCrud('Album', 'fa-solid fa-utensils', Album::class);

        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}
