<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;
use App\Security\JwtService;
use App\Entity\User;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\Parents;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Dto\User\RegisterUserDto;

class AuthController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(
        Request $request, 
        UserRepository $userRepository,
        JwtService $jwtService,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse{
        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) {
            return $this->json(['error' => 'Payload JSON invalide'], 400);
        }

        $email = is_string($data['email'] ?? null) ? trim($data['email']) : null;
        $password = is_string($data['password'] ?? null) ? $data['password'] : null;

        if (!$email || !$password) {
            return $this->json(['error' => 'Email et mot de passe requis'], 400);
        }

        $user = $userRepository->findOneBy(['email' => $email]);
        

        if (!$user || !$passwordHasher->isPasswordValid($user, $password)) {
            return $this->json(['error' => 'Identifiants invalides'], 401);
        }

        $token = $jwtService->generate([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ]);

        return $this->json([
            'token' => $token,
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ],
        ], 200);
    }

    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) {
            return $this->json(['error' => 'Payload JSON invalide'], 400);
        }

        $dto = RegisterUserDto::fromArray($data);
        $errors = $dto->validate();
        if ($errors !== []) {
            return $this->json(['errors' => $errors], 400);
        }

        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $dto->email]);
        if ($existingUser) {
            return $this->json(['error' => 'Email déjà utilisé'], 409);
        }

        $user = match ($dto->role) {
            'ROLE_STUDENT' => new Student(),
            'ROLE_TEACHER' => new Teacher(),
            'ROLE_PARENT' => new Parents(),
            default => new User(),
        };
        $user->setFirstName($dto->firstName);
        $user->setLastName($dto->lastName);
        $user->setEmail($dto->email);
        $user->setPassword($passwordHasher->hashPassword($user, $dto->password));
        $user->setRoles([$dto->role]);

        if ($user instanceof Student) {
            $user->setEnrollmentDate(new \DateTime());
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'message' => 'Inscription réussie',
        ], 201);
    }

    #[Route('/api/logout', name: 'api_logout', methods: ['POST'])]
    public function logout(): JsonResponse
    {
        return $this->json(['message' => 'Vous êtes bien déconnecté']);
    }
}