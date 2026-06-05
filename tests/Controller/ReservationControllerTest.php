<?php

namespace App\Tests\Controller;

use App\Entity\Restaurant;
use App\Entity\User;
use App\Factory\ReservationFactory;
use App\Factory\RestaurantFactory;
use App\Factory\UserFactory;
use App\Repository\ReservationRepository;
use DateTime;
use Override;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class ReservationControllerTest extends WebTestCase
{
    use ResetDatabase, Factories;

    private KernelBrowser $client;
    private Restaurant $restaurant;
    private User $user;
    private ReservationRepository $reservationRepository;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->restaurant = RestaurantFactory::createOne();
        $this->user = UserFactory::createOne();

        $container = static::getContainer();
        $this->reservationRepository = $container->get(ReservationRepository::class);
    }


    public function testIndexWithoutLogin(): void
    {
        $this->client->request('GET', '/reservation');
        self::assertResponseRedirects('/login');
    }


    public function testIndexLoginNoReservation(): void
    {
        $this->client->loginUser($this->user);

        $this->client->request('GET', '/reservation');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('tbody', 'No reservations found');
    }


    public function testIndexWithReservation(): void
    {
        ReservationFactory::createOne([
            'customer' => $this->user,
            'restaurant' => $this->restaurant,
        ]);

        $this->client->loginUser($this->user);
        $this->client->request('GET', '/reservation');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextNotContains('tbody', 'No reservations found');
    }
    

    public function testNewWithoutLogin(): void
    {
        $this->client->request('GET', '/reservation/'. (String) $this->restaurant->getId(). '/new');
        self::assertResponseRedirects('/login');
    }


    public function testNewWithLogin(): void
    {
        $this->client->loginUser($this->user);
        $this->client->request('GET', '/reservation/'. (String) $this->restaurant->getId(). '/new');
        self::assertResponseIsSuccessful();
    }

    
    public function testNewSubmit(): void
    {
        $tomorrow = new DateTime('tomorrow');

        $this->client->loginUser($this->user);
        $this->client->request('GET', '/reservation/'. (String) $this->restaurant->getId(). '/new');
        $this->client->submitForm('Reserve', [
            'reservation[numberOfPeople]'               => 1,
            'reservation[dateAndTime][date]'            => $tomorrow->format('Y-m-d'),
            'reservation[dateAndTime][time][hour]'      => '18',
            'reservation[dateAndTime][time][minute]'    => '0',
        ]);
        self::assertResponseRedirects('/reservation');
        self::assertCount(1, $this->reservationRepository->findAll());
    }


    public function testNewNoSeats():void
    {
        $tomorrow = new Datetime('tomorrow')->setTime(18,0);
        ReservationFactory::createOne([
            'restaurant'    => $this->restaurant,
            'customer'      => $this->user,
            'dateAndTime'   => $tomorrow,
        ]);

        $this->client->loginUser($this->user);
        $this->client->request('GET', '/reservation/'. (String) $this->restaurant->getId(). '/new');
        $this->client->submitForm('Reserve', [
            'reservation[numberOfPeople]'               => 1,
            'reservation[dateAndTime][date]'            => $tomorrow->format('Y-m-d'),
            'reservation[dateAndTime][time][hour]'      => '18',
            'reservation[dateAndTime][time][minute]'    => '0',
        ]);

        self::assertSelectorTextContains('body', 'Seats unavailable at this date and time');
        self::assertCount(1, $this->reservationRepository->findAll());
    }


    public function testAccessEditWithoutLogin(): void
    {
        $reservation = ReservationFactory::createOne([
            'customer' => $this->user,
        ]);

        $this->client->request('GET', '/reservation/'. (String) $reservation->getId(). '/edit');
        self::assertResponseRedirects('/login');
    }


    public function testAccessEdit(): void
    {
        $reservation = ReservationFactory::createOne([
            'customer' => $this->user,
        ]);
        
        $this->client->loginUser($this->user);
        $this->client->request('GET', '/reservation/'. (String) $reservation->getId(). '/edit');
        self::assertResponseIsSuccessful();
    }


    public function testAccessEditOthers(): void
    {
        $reservation = ReservationFactory::createOne([
            'customer' => $this->user,
        ]);

        $otherUser = UserFactory::createOne();
        $this->client->loginUser($otherUser);
        $this->client->request('GET', '/reservation/'. (String) $reservation->getId(). '/edit');
        self::assertResponseStatusCodeSame(403);
    }

    
    public function testEdit(): void
    {
        $tomorrow = new DateTime('tomorrow')->setTime(18, 0);
        $reservation = ReservationFactory::createOne([
            'customer'          => $this->user,
            'numberOfPeople'    => 1,
            'dateAndTime'       => $tomorrow,
        ]);
        

        $this->client->loginUser($this->user);
        $this->client->request('GET', '/reservation/'. (String) $reservation->getId(). '/edit');
        $this->client->submitForm('Change Reservation', [
            'reservation[numberOfPeople]'               => 5,
            'reservation[dateAndTime][date]'            => $tomorrow->format('Y-m-d'),
            'reservation[dateAndTime][time][hour]'      => '18',
            'reservation[dateAndTime][time][minute]'    => '0',
        ]);

        self::assertResponseRedirects('/reservation');

        $updated = $this->reservationRepository->find($reservation->getId());
        self::assertSame(5, $updated->getNumberOfPeople());
    }


    public function testDelete(): void
    {
        $reservation = ReservationFactory::createOne([
            'customer' => $this->user,
        ]);

        $this->client->loginUser($this->user);
        $this->client->request('POST', '/reservation/'. (String) $reservation->getId(). '/delete');
        self::assertResponseRedirects('/reservation');
        self::assertCount(0, $this->reservationRepository->findAll());
    }
}
