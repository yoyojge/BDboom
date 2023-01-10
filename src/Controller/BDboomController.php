<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Repository\BDboomRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/')]
class BDboomController extends AbstractController
{
    #[Route('/', name: 'app_BDboom_index', methods: ['GET'])]
    public function index(UserRepository $userRepository, BDboomRepository $BDboomRepository): Response
    {
        
          
        $tabActu = $BDboomRepository->RSS('https://bdboom.fr/blog/category/actu/feed/' );
        $tabBleu = $BDboomRepository->RSS('https://bdboom.fr/blog/category/bleucomme/feed/' );
        $tabBoom = $BDboomRepository->RSS('https://bdboom.fr/blog/category/quifaitboom/feed/' );
         
        // dd($tabBleu);
        
        return $this->render('BDboom/index.html.twig', [
            'tabActu' => $tabActu,
            'tabBleu' => $tabBleu,
            'tabBoom' => $tabBoom,
        ]);
    }


    #[Route('/miaou', name: 'app_BDboom_miaou', methods: ['GET'])]
    public function miaou()
    {            
        return $this->render('BDboom/miaou.html.twig', []);
    }

    
}
