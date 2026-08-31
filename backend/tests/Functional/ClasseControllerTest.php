<?php

namespace App\Tests;

use App\Entity\User;
use App\Security\JwtService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ClasseControllerTest extends WebTestCase
{
    public function testAdminCanListClasses(): void
    {
        $client = static::createClient();
        $container = self::getContainer();
        $entityManager = $container->get('doctrine')->getManager();

        $user = $entityManager->getRepository(User::class)
            ->findOneBy(['email' => 'admin@primeo.com']);

        $this->assertNotNull($user, 'L’utilisateur admin de test est absent de la base.');

        $jwtService = $container->get(JwtService::class);
        $token = $jwtService->generate([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ]);

        $client->setServerParameter('HTTP_AUTHORIZATION', 'Bearer ' . $token);
        $client->request('GET', '/api/classes/');

        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(200);

        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($content);
        $this->assertNotEmpty($content);
    }
}