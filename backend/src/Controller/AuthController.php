<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;
use App\Security\JwtService;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

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

        $token = $jwtService->generate([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ]);

        return $this->json(['token' => $token]);
    }

    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['email']) || empty($data['password'])) {
            return $this->json(['error' => 'Email et mot de passe requis'], 400);
        }

        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return $this->json(['error' => 'Email déjà utilisé'], 400);
        }

        $user = new User();
        $user->setFirstName($data['first_name'] ?? null);
        $user->setLastName($data['last_name'] ?? null);
        $user->setEmail($data['email']);
        $user->setPassword($passwordHasher->hashPassword($user, $data['password']));
        switch ($data['role'] ?? 'student') {
            case 'parent':
                $user->setRoles(['ROLE_PARENT']);
                break;
            case 'teacher':
                $user->setRoles(['ROLE_TEACHER']);
                break;
            default:
                $user->setRoles(['ROLE_STUDENT']);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Inscription réussie',
            'email' => $data['email'] ?? null
        ]);
    }

    #[Route('/api/logout', name: 'api_logout', methods: ['POST'])]
    public function logout(): JsonResponse
    {
        return $this->json(['message' => 'Vous êtes bien déconnecté']);
    }
}