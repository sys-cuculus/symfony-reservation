<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Restaurant;
use App\Form\ReservationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

final class ReservationController extends AbstractController
{
    #[Route('/reservation', name: 'app_reservation')]
    public function index(): Response
    {
        return $this->render('reservation/index.html.twig', [
            'controller_name' => 'ReservationController',
        ]);
    }

    #[Route('/reservation/{restaurantId}/new', name: 'app_reservation_new', requirements: ['restaurantId' => Requirement::DIGITS])]
    public function new(
        #[MapEntity(mapping: ['restaurantId' => 'id'])]
        Restaurant $restaurant,
        Request $request,
        EntityManagerInterface $manager,
    ): Response
    {
        $reservation = new Reservation();
        $reservation->setRestaurant($restaurant);
        $form = $this->createForm(ReservationType::class, $reservation);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $reservation = $form->getData();
            $reservation->setRestaurant($restaurant);
            $reservation->setCustomer($this->getUser());

            $manager->persist($reservation);
            $manager->flush();

            return $this->redirectToRoute('app_restaurant');
        }
        return $this->render('reservation/new.html.twig', [
            'restaurant' => $restaurant,
            'openingHours' => $restaurant->getOpeningHours()->getValues(),
            'form' => $form,
        ]);
    }
}
