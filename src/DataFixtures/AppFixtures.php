<?php

namespace App\DataFixtures;

use App\Entity\Restaurant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $restaurant = new Restaurant();
        $restaurant->setRestaurantName('Lyon restaurant');
        $restaurant->setAddress('Lyon');
        $restaurant->setTel(('1234567890'));
        $manager->persist($restaurant);

        $restaurant = new Restaurant();
        $restaurant->setRestaurantName('Paris restaurant2');
        $restaurant->setAddress('Paris');
        $restaurant->setTel(('2234567890'));
        $manager->persist($restaurant);

        $manager->flush();
    }
}
