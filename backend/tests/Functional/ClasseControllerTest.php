<?php

namespace App\Tests;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Security\JwtService;

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
        $jwtService = $container->get(JwtService::class); 

        $token = $jwtService->generate([
            'email' => $user->getEmail()
        ]);

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