<?php

namespace App\Tests\Controller;

use App\Factory\RestaurantFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class RestaurantControllerTest extends WebTestCase
{
    use ResetDatabase, Factories;
    
    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        self::assertResponseIsSuccessful();
    }

    public function testDetail(): void
    {
        $client = static::createClient();

        $restaurant = RestaurantFactory::createOne();
        $client->request('GET', '/restaurant/'. (String)$restaurant->getId());

        self::assertResponseIsSuccessful();
    }
}
