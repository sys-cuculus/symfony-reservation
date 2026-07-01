<?php

namespace App\Tests\Controller;

use App\Entity\Restaurant;
use App\Entity\User;
use App\Factory\ReservationFactory;
use App\Factory\RestaurantFactory;
use App\Factory\UserFactory;
use App\Repository\OpeningHourRepository;
use App\Repository\ReservationRepository;
use App\Repository\RestaurantRepository;
use DateTime;
use Override;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\Container;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class OwnerControllerTest extends WebTestCase
{
    use ResetDatabase, Factories;

    private Container $container;
    private KernelBrowser $client;
    private Restaurant $restaurant;
    private User $owner;
    private string $restaurantDetailUrl;
    private string $baseUrl = '/owner/';


    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->client = static::createClient();
        $this->container = static::getContainer();
        $this->owner = UserFactory::createOne([
            'roles' => ['ROLE_OWNER'],
        ]);

        $this->restaurant = RestaurantFactory::createOne([
            'owner' => $this->owner,
        ]);
        $this->restaurantDetailUrl = $this->baseUrl. 'restaurant/'. (String) $this->restaurant->getId();
    }
    

    public function testDashboardWithoutLogin(): void
    {
        $this->client->request('GET', $this->baseUrl. 'dashboard');

        self::assertResponseRedirects('/login');
    }


    public function testDashboardRoleUser(): void
    {
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $this->client->request('GET', $this->baseUrl. 'dashboard');
        self::assertResponseStatusCodeSame(403);
    }


    public function testDashboardOwner(): void
    {
        $this->client->loginUser($this->owner);
        $this->client->request('GET', $this->baseUrl. 'dashboard');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('body', $this->restaurant->getRestaurantName());
    }


    public function testDashboardOtherOwner(): void
    {
        $otherOwner = UserFactory::createOne([
            'roles' => ['ROLE_OWNER'],
        ]);

        $this->client->loginUser($otherOwner);
        $this->client->request('GET', $this->baseUrl. 'dashboard');
        self::assertSelectorTextNotContains('body', $this->restaurant->getRestaurantName());
    }


    public function testShowWithoutLogin(): void
    {
        $this->client->request('GET', $this->restaurantDetailUrl);
        self::assertResponseRedirects('/login');
    }


    public function testShowRoleUser(): void
    {
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $this->client->request('GET', $this->restaurantDetailUrl);
        self::assertResponseStatusCodeSame(403);
    }


    public function testShow(): void
    {
        $this->client->loginUser($this->owner);
        $this->client->request('GET', $this->restaurantDetailUrl);
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('body', $this->restaurant->getRestaurantName());
    }


    public function testShowOtherOwner(): void
    {
        $otherOwner = UserFactory::createOne([
            'roles' => ['ROLE_OWNER'],
        ]);
        $this->client->loginUser($otherOwner);
        $this->client->request('GET', $this->restaurantDetailUrl);
        self::assertResponseStatusCodeSame(403);
    }


    public function testAccessNewWithoutLogin(): void
    {
        $this->client->request('GET', $this->baseUrl. 'restaurant/new');
        self::assertResponseRedirects('/login');
    }


    public function testAccessNewRoleUser(): void
    {
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $this->client->request('GET', $this->baseUrl. 'restaurant/new');
        self::assertResponseStatusCodeSame(403);
    }


    public function testNew(): void
    {
        $this->client->loginUser($this->owner);
        $this->client->request('GET', $this->baseUrl. 'restaurant/new');
        self::assertResponseIsSuccessful();

        $this->client->submitForm('Create', [
            'restaurant[restaurant_name]'   => 'test restaurant',
            'restaurant[address]'           => 'test restaurant address',
            'restaurant[tel]'               => '0912345678',
        ]);

        self::assertResponseRedirects($this->baseUrl. 'dashboard');
        self::assertCount(1, $this->container->get(RestaurantRepository::class)->findBy([
            'restaurant_name'   => 'test restaurant',
            'address'           => 'test restaurant address',
            'tel'               => '0912345678',
        ]));
    }


    public function testDelete(): void
    {
        $this->client->loginUser($this->owner);
        $this->client->request('POST', $this->restaurantDetailUrl. '/delete');
        self::assertResponseRedirects($this->baseUrl. 'dashboard');
        self::assertCount(0, $this->container->get(RestaurantRepository::class)->findAll());
    }


    public function testOpeningHourWithoutLogin(): void
    {
        $this->client->request('GET', $this->restaurantDetailUrl. '/opening-hours');
        self::assertResponseRedirects('/login');

    }


    public function testOpeningHoursRoleUser(): void
    {
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $this->client->request('GET', $this->restaurantDetailUrl. '/opening-hours');
        self::assertResponseStatusCodeSame(403);
    }


    public function testOpeningHourOtherOwner(): void
    {
        $otherOwner = UserFactory::createOne([
            'roles' => ['ROLE_OWNER'],
        ]);
        $this->client->loginUser($otherOwner);
        $this->client->request('GET', $this->restaurantDetailUrl. '/opening-hours');
        self::assertResponseStatusCodeSame(403);
    }


    public function testOpeningHour(): void
    {
        $this->client->loginUser($this->owner);
        $this->client->request('GET', $this->restaurantDetailUrl. '/opening-hours');
        self::assertResponseIsSuccessful();

        $this->client->submitForm('Save', [
            'opening_hours_form[openingHours][0][dayOfWeek]'    => '1',
            'opening_hours_form[openingHours][0][openTime]'     => '09:00',
            'opening_hours_form[openingHours][0][closeTime]'    => '18:00',
            'opening_hours_form[openingHours][0][closedFlag]'   => false,

            'opening_hours_form[openingHours][1][dayOfWeek]'    => '2',
            'opening_hours_form[openingHours][1][openTime]'     => '10:00',
            'opening_hours_form[openingHours][1][closeTime]'    => '18:00',
            'opening_hours_form[openingHours][1][closedFlag]'   => false,

            'opening_hours_form[openingHours][2][dayOfWeek]'    => '3',
            'opening_hours_form[openingHours][2][openTime]'     => '11:00',
            'opening_hours_form[openingHours][2][closeTime]'    => '18:00',
            'opening_hours_form[openingHours][2][closedFlag]'   => false,

            'opening_hours_form[openingHours][3][dayOfWeek]'    => '4',
            'opening_hours_form[openingHours][3][openTime]'     => '12:00',
            'opening_hours_form[openingHours][3][closeTime]'    => '18:00',
            'opening_hours_form[openingHours][3][closedFlag]'   => false,

            'opening_hours_form[openingHours][4][dayOfWeek]'    => '5',
            'opening_hours_form[openingHours][4][openTime]'     => '13:00',
            'opening_hours_form[openingHours][4][closeTime]'    => '18:00',
            'opening_hours_form[openingHours][4][closedFlag]'   => false,

            'opening_hours_form[openingHours][5][dayOfWeek]'    => '6',
            'opening_hours_form[openingHours][5][openTime]'     => '',
            'opening_hours_form[openingHours][5][closeTime]'    => '',
            'opening_hours_form[openingHours][5][closedFlag]'   => true,

            'opening_hours_form[openingHours][6][dayOfWeek]'    => '7',
            'opening_hours_form[openingHours][6][openTime]'     => '',
            'opening_hours_form[openingHours][6][closeTime]'    => '',
            'opening_hours_form[openingHours][6][closedFlag]'   => true,
        ]);

        $this->assertResponseRedirects($this->restaurantDetailUrl. '/opening-hours');
        $this->assertCount(1, $this->container->get(OpeningHourRepository::class)->findBy([
            'restaurant'    => $this->restaurant,
            'dayOfWeek'     => 1,
            'openTime'      => new DateTime()->settime(9, 0, 0),
        ]));

        $this->assertCount(1, $this->container->get(OpeningHourRepository::class)->findBy([
            'restaurant'    => $this->restaurant,
            'dayOfWeek'     => 2,
            'openTime'      => new DateTime()->settime(10, 0, 0),
        ]));

        $this->assertCount(1, $this->container->get(OpeningHourRepository::class)->findBy([
            'restaurant'    => $this->restaurant,
            'dayOfWeek'     => 3,
            'openTime'      => new DateTime()->settime(11, 0, 0),
        ]));

        $this->assertCount(1, $this->container->get(OpeningHourRepository::class)->findBy([
            'restaurant'    => $this->restaurant,
            'dayOfWeek'     => 4,
            'openTime'      => new DateTime()->settime(12, 0, 0),
        ]));

        $this->assertCount(1, $this->container->get(OpeningHourRepository::class)->findBy([
            'restaurant'    => $this->restaurant,
            'dayOfWeek'     => 5,
            'openTime'      => new DateTime()->settime(13, 0, 0),
        ]));

        $this->assertCount(1, $this->container->get(OpeningHourRepository::class)->findBy([
            'restaurant'    => $this->restaurant,
            'dayOfWeek'     => 6,
            'closedFlag'    => true,
        ]));

        $this->assertCount(1, $this->container->get(OpeningHourRepository::class)->findBy([
            'restaurant'    => $this->restaurant,
            'dayOfWeek'     => 7,
            'closedFlag'    => true,
        ]));
    }


    public function testEditReservationOtherOwner(): void
    {
        $otherOwner = UserFactory::createOne([
            'roles' => ['ROLE_OWNER',]
        ]);

        $reservation = ReservationFactory::createOne([
            'restaurant'    => $this->restaurant,
        ]);

        $this->client->loginUser($otherOwner);
        $this->client->request('GET', '/reservation/'. (String) $reservation->getId() . '/edit');
        $this->assertResponseStatusCodeSame(403);
    }


    public function testEditReservation(): void
    {
        $tomorrow = new DateTime('tomorrow')->setTime(18, 0);
        $reservation = ReservationFactory::createOne([
            'restaurant'        => $this->restaurant,
            'numberOfPeople'    => 1,
            'dateAndTime'       => $tomorrow,
        ]);

        $this->client->loginUser($this->owner);
        $this->client->request('GET', '/reservation/'. (String) $reservation->getId(). '/edit');
        $this->assertResponseIsSuccessful();

        $this->client->submitForm('Change Reservation', [
            'reservation[numberOfPeople]'               => 5,
            'reservation[dateAndTime][date]'            => $tomorrow->format('Y-m-d'),
            'reservation[dateAndTime][time][hour]'      => '18',
            'reservation[dateAndTime][time][minute]'    => '0',
        ]);

        $updated = $this->container->get(ReservationRepository::class)->find($reservation->getId());
        $this->assertSame(5, $updated->getNumberOfPeople());
    }

}
