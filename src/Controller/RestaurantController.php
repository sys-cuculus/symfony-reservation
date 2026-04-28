<?php

namespace App\Controller;

use App\Repository\RestaurantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RestaurantController extends AbstractController
{
    #[Route('/', name: 'app_restaurant')]
    public function index(RestaurantRepository $repository): Response
    {
        return $this->render('restaurant/index.html.twig', [
            'restaurants' => $repository->findAll(),
        ]);
    }
}
