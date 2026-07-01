<?php

namespace App\Controller;

use App\Entity\OpeningHour;
use App\Entity\Restaurant;
use App\Entity\User;
use App\Form\OpeningHoursFormType;
use App\Form\OpeningHourType;
use App\Form\RestaurantType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
        return $this->render('owner/restaurant/index.html.twig', [
            'restaurants' => $user->getRestaurants()->getValues(),
        ]);
    }


    #[Route('/restaurant/{id}', name: 'app_restaurant_detail', requirements: ['id' => Requirement::DIGITS])]
    #[IsGranted('view', subject: 'restaurant')]
    public function show(#[CurrentUser] User  $user, Restaurant $restaurant): Response
    {
        if ($user->getId() !== $restaurant->getOwner()->getId()) {
            throw $this->createAccessDeniedException();
        }
        
        return $this->render('owner/restaurant/show.html.twig', [
            'restaurant' => $restaurant,
            'openingHours' => $restaurant->getOpeningHours()->getValues(),
            'reservations' => $restaurant->getReservations()->getValues(),
        ]);
    }

    #[Route('/restaurant/new', name: 'app_restaurant_new')]
    public function new(
        #[CurrentUser] User $user,
        Request $reauest,
        EntityManagerInterface $manager,
    ): Response
    {
        $form = $this->createForm(RestaurantType::class);

        $form->handleRequest($reauest);
        if ($form->isSubmitted() && $form->isValid()) {
            $restaurant = $form->getData();
            $restaurant
                ->setOwner($user)
                ->initializeOpeningHours();
            
            $manager->persist($restaurant);
            $manager->flush();
            $this->addFlash('notice', 'Restaurant created successfully');
            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('owner/restaurant/new.html.twig', [
            'form' => $form,
        ]);
    }


    #[Route('/restaurant/{id}/delete', name: 'app_restaurant_delete', requirements: ['id' => Requirement::DIGITS])]
    #[IsGranted('edit', subject: 'restaurant')]
    public function delete(Restaurant $restaurant, EntityManagerInterface $manager): Response
    {
        $manager->remove($restaurant);
        $manager->flush();
        $this->addFlash('notice', 'Restaurant deleted successfully');
        
        return $this->redirectToRoute('app_dashboard');
    }



    #[Route('/restaurant/{id}/opening-hours', name: 'app_restaurant_opening_hours', requirements: ['id' => Requirement::DIGITS])]
    #[IsGranted('edit', subject: 'restaurant')]
    public function openingHours(
        #[CurrentUser] User  $user,
        Restaurant $restaurant,
        Request $request,
        EntityManagerInterface $manager,
        ): Response
    {
        if ($user->getId() !== $restaurant->getOwner()->getId()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(OpeningHoursFormType::class, [
            'openingHours'  => $restaurant->getOpeningHours(),
        ], [
            'method' => 'POST',
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();
            $this->addFlash('notice', 'Working hours set successfully');

            return $this->redirectToRoute('app_restaurant_opening_hours', [
                'id' => $restaurant->getId(),
            ]);
        }

        return $this->render('owner/restaurant/openingHours.html.twig', [
            'restaurant'    => $restaurant,
            'openingHours'  => $restaurant->getOpeningHours()->getValues(),
            'form'          => $form
        ]);
    }

}
