<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/api/users')]
class UserController extends AbstractController
{
    #[Route('/', name: 'api_users', methods: ['GET'])]
    public function index(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();

        return $this->json($users, 200, [], ['groups' => 'user:read']);
    }

    #[Route('/{id}', name: 'api_user', methods: ['GET'])]
    public function show(UserRepository $userRepository, int $id): JsonResponse
    {
        $user = $userRepository->find($id); 
        if (!$user) {
            return $this->json(['error' => 'Utilisateur non trouvé'], 404);
        }
        $groups = 'user:read';
        $roles = $user->getRoles();
        $roles = $user->getRoles();

        
        if (in_array('ROLE_STUDENT', $roles)) {
            $groups = 'student:read';
        } elseif (in_array('ROLE_TEACHER', $roles)) {
            $groups = 'teacher:read';
        } elseif (in_array('ROLE_PARENT', $roles)) {
            $groups = 'parent:read';
        }

        return $this->json($user, 200, [], ['groups' => $groups]);
    }

    #[Route('/{id}', name: 'api_user_update', methods: ['PUT'])]
    public function update(UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        int $id): JsonResponse
    {
        $user = $userRepository->find($id);
        if (!$user) {
            return $this->json(['error' => 'Utilisateur non trouvé'], 404);
        }

        $data = json_decode($request->getContent(), true);
        $user->setFirstName($data['firstName'] ?? $user->getFirstName());
        $user->setLastName($data['lastName'] ?? $user->getLastName());
        $user->setEmail($data['email'] ?? $user->getEmail());
        if (!empty($data['password'])) {
            $user->setPassword(
                $passwordHasher->hashPassword($user, $data['password'])
            );
        }
        $entityManager->flush();

        return $this->json(['message' => 'Utilisateur mis à jour avec succès'], 200);
    }

    #[Route('/{id}', name: 'api_user_delete', methods: ['DELETE'])]
    public function delete(UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        int $id): JsonResponse
    {
        $user = $userRepository->find($id);
        if (!$user) {
            return $this->json(['error' => 'Utilisateur non trouvé'], 404);
        }

        $entityManager->remove($user);
        $entityManager->flush();

        return $this->json(['message' => 'Utilisateur supprimé avec succès'], 200);
    }
}