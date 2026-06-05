<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class OwnerControllerTest extends WebTestCase
{
    use ResetDatabase, Factories;

    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/owner/dashboard');

        self::assertResponseRedirects('/login');
    }
}
