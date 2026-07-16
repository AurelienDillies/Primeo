<?php

namespace App\Controller;

use App\Entity\Classe;
use App\Repository\ClasseRepository;
use App\Repository\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/classes')]
class ClasseController extends AbstractController
{
    #[Route('/', name: 'get_classes', methods: ['GET'])]
    public function getClasses(ClasseRepository $classeRepository, SerializerInterface $serializer): JsonResponse
    {
        $classes = $classeRepository->findAll();
        try{
            $json = $serializer->serialize($classes, 'json', ['groups' => 'classe:read']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur de sérialisation: ' . $e->getMessage()], 500);
        }
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/{id}', name: 'get_class', methods: ['GET'])]
    public function getClasse(ClasseRepository $classeRepository, SerializerInterface $serializer, int $id): JsonResponse
    {
        $classe = $classeRepository->find($id);

        if (!$classe) {
            return new JsonResponse(['error' => 'Classe non trouvée'], 404);
        }

        try{
            $json = $serializer->serialize($classe, 'json', ['groups' => 'classe:read']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur de sérialisation: ' . $e->getMessage()], 500);
        }
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/{id}/courses', name: 'get_class_courses', methods: ['GET'])]
    public function getClasseCourses(
        int $id,
        ClasseRepository $classeRepository,
        CourseRepository $courseRepository,
        SerializerInterface $serializer
    ): JsonResponse {
        if (
            !$this->isGranted('ROLE_ADMIN')
            && !$this->isGranted('ROLE_TEACHER')
            && !$this->isGranted('ROLE_STUDENT')
        ) {
            throw $this->createAccessDeniedException('Accès refusé.');
        }

        $classe = $classeRepository->find($id);
        if (!$classe) {
            return new JsonResponse(['error' => 'Classe non trouvée'], 404);
        }

        try {
            $json = $serializer->serialize($courseRepository->findByClasseIdForRead($id), 'json', ['groups' => 'classe:read']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur de sérialisation: ' . $e->getMessage()], 500);
        }

        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/', name: 'create_class', methods: ['POST'])]
    public function createClasse(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $classe = new Classe();
        $classe->setClassName($data['className'] ?? null);
        $classe->setClassDescription($data['classDescription'] ?? null);
        $entityManager->persist($classe);
        $entityManager->flush();
        return new JsonResponse(['message' => 'Classe créée avec succès', 'id' => $classe->getId()], 201);
    }

    #[Route('/{id}', name: 'update_class', methods: ['PUT'])]
    public function updateClasse(
        Request $request,
        EntityManagerInterface $entityManager,
        ClasseRepository $classeRepository,
        int $id
    ): JsonResponse {
        $classe = $classeRepository->find($id);
        if (!$classe) {
            return new JsonResponse(['error' => 'Classe non trouvée'], 404);
        }
        $data = json_decode($request->getContent(), true);
        $classe->setClassName($data['className'] ?? null);
        $classe->setClassDescription($data['classDescription'] ?? null);
        $entityManager->flush();
        return new JsonResponse(['message' => 'Classe mise à jour avec succès'], 200);
    }

    #[Route('/{id}', name: 'delete_class', methods: ['DELETE'])]
    public function deleteClasse(
        EntityManagerInterface $entityManager,
        ClasseRepository $classeRepository,
        int $id
    ): JsonResponse {
        $classe = $classeRepository->find($id);
        if (!$classe) {
            return new JsonResponse(['error' => 'Classe non trouvée'], 404);
        }
        $entityManager->remove($classe);
        $entityManager->flush();
        return new JsonResponse(['message' => 'Classe supprimée avec succès'], 200);
    }
}
