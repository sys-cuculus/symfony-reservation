<?php

namespace App\DataFixtures;

use App\Entity\OpeningHour;
use App\Entity\Restaurant;
use App\Entity\User;
use App\Enum\DayOfWeek;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }


    public function load(ObjectManager $manager): void
    {
        // create a normal user
        $user = new User();
        $password = $this->hasher->hashPassword($user, 'testuser');
        $user   ->setEmail('testuser@gmail.com')
                ->setPassword($password);

        $manager->persist($user);

        // create an admin user
        $user = new User();
        $password = $this->hasher->hashPassword($user, 'testowner');

        $user   ->setEmail('testowner@gmail.com')
                ->setPassword($password)
                ->setRoles(['ROLE_OWNER']);
        $manager->persist($user);

        $restaurant = new Restaurant();
        $restaurant ->setRestaurantName('Lyon restaurant')
                    ->setAddress('Lyon')
                    ->setTel(('1234567890'))
                    ->setOwner($user)
                    ->initializeOpeningHours();
        $manager->persist($restaurant);

        foreach (DayOfWeek::cases() as $day) {
            $openingHour = new OpeningHour();
            $openingHour->setDayOfWeek($day)
                        ->setRestaurant($restaurant)
                        ->setOpenTime(DateTime::createFromFormat('H:i', '9:00'))
                        ->setCloseTime(DateTime::createFromFormat('H:i', '23:00'));
        }

        $manager->flush();
    }
}
