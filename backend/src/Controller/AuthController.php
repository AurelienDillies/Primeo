<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;
use App\Security\JwtService;
use App\Entity\User;

class AuthController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(
        Request $request, 
        UserRepository $userRepository,
        JwtService $jwtService
    ): JsonResponse{
        $data = json_decode($request->getContent(), true);

        $user = $userRepository->findOneBy(['email' => $data['email'] ?? null]);

        if (!$user || !password_verify($data['password'], $user->getPassword())) {
            return $this->json(['error' => 'Identifiants invalides'], 401);
        }

        // ✅ IMPORTANT : on génère le token avec un tableau (payload)
        $token = $jwtService->generate([
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ]);

        return $this->json(['token' => $token]);
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