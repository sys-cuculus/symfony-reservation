<?php

namespace App\Controller;

use App\Entity\OpeningHour;
use App\Entity\Reservation;
use App\Entity\Restaurant;
use App\Entity\User;
use App\Form\ReservationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class ReservationController extends AbstractController
{
    #[Route('/reservation', name: 'app_reservation')]
    public function index(#[CurrentUser] User  $user): Response
    {
        return $this->render('reservation/index.html.twig', [
            'user' => $user,
            'reservations' => $user->getReservations()->getValues(),
        ]);
    }

    #[Route(
        '/reservation/{restaurantId}/new',
        name: 'app_reservation_new',
        requirements: ['restaurantId' => Requirement::DIGITS]
    )]
    public function new(
        #[MapEntity(mapping: ['restaurantId' => 'id'])]
        Restaurant $restaurant,
        Request $request,
        EntityManagerInterface $manager,
    ): Response
    {
        $openingHours = $restaurant->getOpeningHours()->getValues();
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
            $this->addFlash('notice', 'Resered successfully');


            return $this->redirectToRoute('app_restaurant');
        }
        
        return $this->render('reservation/new.html.twig', [
            'restaurant' => $restaurant,
            'openingHoursData' => $this->formatOpeningHoursForJavascript($openingHours),
            'form' => $form,
        ]);
    }

    #[Route('/reservation/{id}/edit', name: 'app_reservation_edit')]
    public function edit(
        Reservation $reservation,
        Request $request,
        EntityManagerInterface $manager,
    ): Response
    {
        $openingHours = $reservation->getRestaurant()->getOpeningHours()->getValues();
        $form = $this->createForm(ReservationType::class, $reservation);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();
            $this->addFlash('notice', 'Reservation changed successfully');

            return $this->redirectToRoute('app_reservation');
        }

        return $this->render('reservation/edit.html.twig', [
            'form' => $form,
            'openingHoursData' => $this->formatOpeningHoursForJavascript($openingHours),
        ]);
    }

    /**
     * @param OpeningHour[] $openingHours
     */
    private function formatOpeningHoursForJavascript(array $openingHours): array
    {
        return array_map(static fn (OpeningHour $openingHour): array => [
            'dayOfWeek' => $openingHour->getDayOfWeek()->value,
            'openTime' => $openingHour->getOpenTime()?->format('H:i'),
            'closeTime' => $openingHour->getCloseTime()?->format('H:i'),
            'closed' => $openingHour->isClosedFlag(),
        ], $openingHours);
    }
}
