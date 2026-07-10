<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;

#[Route('/api/users')]
class UserController extends AbstractController
{
    #[Route('/', name: 'api_users', methods: ['GET'])]
    public function index(UserRepository $userRepository): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $users = $userRepository->findAll();

        return $this->json($users, 200, [], ['groups' => 'user:read']);
    }

    #[Route('/me', name: 'api_user_me', methods: ['GET'])]
    public function me(UserRepository $userRepository): JsonResponse
    {
        $currentUser = $this->getUser();
        if (!$currentUser instanceof User) {
            return $this->json(['error' => 'Utilisateur non authentifié'], 401);
        }

        $readGroup = $this->resolveReadGroup($currentUser);
        $userForRead = $userRepository->findOneForApiRead($currentUser->getId(), $readGroup) ?? $currentUser;

        return $this->json($userForRead, 200, [], ['groups' => $readGroup]);
    }

    #[Route('/me', name: 'api_user_me_update', methods: ['PUT'])]
    public function meUpdate(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        Request $request,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $currentUser = $this->getUser();
        if (!$currentUser instanceof User) {
            return $this->json(['error' => 'Utilisateur non authentifié'], 401);
        }

        return $this->doUpdate($userRepository, $entityManager, $request, $passwordHasher, $currentUser->getId());
    }

    #[Route('/{id}', name: 'api_user', methods: ['GET'], requirements: ['id' => '\\d+'])]
    public function show(UserRepository $userRepository, int $id): JsonResponse
    {
        $currentUser = $this->getUser();
        if (!$currentUser instanceof User) {
            return $this->json(['error' => 'Utilisateur non authentifié'], 401);
        }

        if (!$this->isGranted('ROLE_ADMIN') && $currentUser->getId() !== $id) {
            return $this->json(['error' => 'Accès interdit'], 403);
        }

        $user = $userRepository->find($id);
        if (!$user) {
            return $this->json(['error' => 'Utilisateur non trouvé'], 404);
        }

        $readGroup = $this->resolveReadGroup($user);
        $userForRead = $userRepository->findOneForApiRead($id, $readGroup) ?? $user;

        return $this->json($userForRead, 200, [], ['groups' => $readGroup]);
    }

    #[Route('/{id}', name: 'api_user_update', methods: ['PUT'], requirements: ['id' => '\\d+'])]
    public function update(UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        int $id): JsonResponse
    {
        $currentUser = $this->getUser();
        if (!$currentUser instanceof User) {
            return $this->json(['error' => 'Utilisateur non authentifié'], 401);
        }

        if (!$this->isGranted('ROLE_ADMIN') && $currentUser->getId() !== $id) {
            return $this->json(['error' => 'Accès interdit'], 403);
        }

        return $this->doUpdate($userRepository, $entityManager, $request, $passwordHasher, $id);
    }

    #[Route('/{id}', name: 'api_user_delete', methods: ['DELETE'], requirements: ['id' => '\\d+'])]
    public function delete(UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        int $id): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = $userRepository->find($id);
        if (!$user) {
            return $this->json(['error' => 'Utilisateur non trouvé'], 404);
        }

        $entityManager->remove($user);
        $entityManager->flush();

        return $this->json(null, 204);
    }

    private function doUpdate(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        int $id
    ): JsonResponse {
        $user = $userRepository->find($id);
        if (!$user) {
            return $this->json(['error' => 'Utilisateur non trouvé'], 404);
        }

        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) {
            return $this->json(['error' => 'Payload JSON invalide'], 400);
        }

        $firstName = $data['first_name'] ?? $data['firstName'] ?? $user->getFirstName();
        $lastName = $data['last_name'] ?? $data['lastName'] ?? $user->getLastName();
        $email = $data['email'] ?? $user->getEmail();

        if (!is_string($firstName) || trim($firstName) === '') {
            return $this->json(['error' => 'Le prénom est requis'], 400);
        }

        if (!is_string($lastName) || trim($lastName) === '') {
            return $this->json(['error' => 'Le nom est requis'], 400);
        }

        if (!is_string($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->json(['error' => 'Email invalide'], 400);
        }

        $existingUser = $userRepository->findOneBy(['email' => $email]);
        if ($existingUser && $existingUser->getId() !== $user->getId()) {
            return $this->json(['error' => 'Email déjà utilisé'], 409);
        }

        $user->setFirstName(trim($firstName));
        $user->setLastName(trim($lastName));
        $user->setEmail($email);

        if (!empty($data['password'])) {
            if (!is_string($data['password']) || strlen($data['password']) < 6) {
                return $this->json(['error' => 'Le mot de passe doit contenir au moins 6 caractères'], 400);
            }

            $user->setPassword($passwordHasher->hashPassword($user, $data['password']));
        }

        $entityManager->flush();

        return $this->json($user, 200, [], ['groups' => $this->resolveReadGroup($user)]);
    }

    private function resolveReadGroup(User $user): string
    {
        $roles = $user->getRoles();

        if (in_array('ROLE_STUDENT', $roles, true)) {
            return 'student:read';
        }

        if (in_array('ROLE_TEACHER', $roles, true)) {
            return 'teacher:read';
        }

        if (in_array('ROLE_PARENT', $roles, true)) {
            return 'parent:read';
        }

        return 'user:read';
    }
}