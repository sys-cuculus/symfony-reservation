<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/owner')]
#[IsGranted('ROLE_OWNER')]
final class OwnerController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(#[CurrentUser] User  $user): Response
    {
        return $this->render('owner/index.html.twig', [
            'restaurants' => $user->getRestaurants()->getValues(),
        ]);
    }

    #[Route('/restaurant/{id}', name: 'app_restaurant_detail', requirements: ['id' => Requirement::DIGITS])]
    public function show(#[CurrentUser] User  $user, Restaurant $restaurant): Response
    {
        if ($user->getId() !== $restaurant->getOwner()->getId()) {
            throw $this->createAccessDeniedException();
        }
        return $this->render('owner/show.html.twig', [
            'restaurant' => $restaurant,
            'openingHours' => $restaurant->getOpeningHours()->getValues(),
            'reservations' => $restaurant->getReservations()->getValues(),
        ]);
    }
}
