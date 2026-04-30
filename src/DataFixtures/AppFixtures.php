<?php

namespace App\DataFixtures;

use App\Entity\Restaurant;
use App\Entity\User;
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
        $user = new User();
        $user->setEmail('testowner@gmail.com');
        $password = $this->hasher->hashPassword($user, 'testowner');
        $user->setPassword($password);
        $user->setRoles(['ROLE_OWNER']);
        $manager->persist($user);

        $restaurant = new Restaurant();
        $restaurant->setRestaurantName('Lyon restaurant');
        $restaurant->setAddress('Lyon');
        $restaurant->setTel(('1234567890'));
        $restaurant->setOwner($user);
        $manager->persist($restaurant);


        $manager->flush();
    }
}
