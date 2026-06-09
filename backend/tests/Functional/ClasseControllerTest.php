<?php

namespace App\Tests;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ClasseControllerTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();

        $container = self::getContainer();
        $em = $container->get('doctrine')->getManager();

        $user = $em->getRepository(User::class)
            ->findOneBy(['email' => 'admin@digiforma.com']);

        $this->assertNotNull($user, 'User not found in database');
      
        $token = $container
            ->get('lexik_jwt_authentication.jwt_manager')
            ->create($user);

        $client->setServerParameter(
            'HTTP_AUTHORIZATION',
            'Bearer ' . $token
        );
        $client->request(
            'GET','/api/classes/');

        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(200);
    }
}