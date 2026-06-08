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

        // 1. Créer un user de test
        $user = new User();
        $user->setEmail('test@test.com');
        $user->setPassword('password');
        $user->setRoles(['ROLE_USER']);
        $user->setFirstName('Test');
        $user->setLastName('User');

        $em->persist($user);
        $em->flush();

        $user = $em->getRepository(User::class)
            ->findOneBy(['email' => 'test@test.com']);
        // 2. Générer le token JWT
        $token = $container
            ->get('lexik_jwt_authentication.jwt_manager')
            ->create($user);

        // 3. Appel API avec token
        $client->request(
            'GET',
            '/api/classes',
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            ]
        );

        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(200);
    }
}