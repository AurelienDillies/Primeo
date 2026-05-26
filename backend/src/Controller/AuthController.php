<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;

class AuthController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        return $this->json([
            'success' => true,
            'message' => 'Login works',
            'email' => $data['email'] ?? null
        ]);
    }

    #[Route('/api/register', name: 'api_register', methods: ['GET'])]
    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        return $this->json([
            'success' => true,
            'message' => 'Register works',
            'username' => $data['username'] ?? null,
            'email' => $data['email'] ?? null
        ]);
    }

    #[Route('/api/users', name: 'api_users', methods: ['GET'])]
    public function listUsers(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();

        $data = array_map(function ($user) {
            return [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'first_name' => $user->getFirstName(),
                'last_name' => $user->getLastName(),
                'created_at' => $user->getCreatedAt()->format('c'),
            ];
        }, $users);

        return $this->json($data);
    }
}