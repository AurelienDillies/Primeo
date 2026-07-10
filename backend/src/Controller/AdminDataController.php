<?php

namespace App\Controller;

use App\Repository\ActivityRepository;
use App\Repository\ClasseRepository;
use App\Repository\CourseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/admin')]
class AdminDataController extends AbstractController
{
    #[Route('/classes', name: 'api_admin_classes', methods: ['GET'])]
    public function classes(ClasseRepository $classeRepository, SerializerInterface $serializer): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->serializeCollection($serializer, $classeRepository->findAllForAdminRead(), 'classe:read');
    }

    #[Route('/courses', name: 'api_admin_courses', methods: ['GET'])]
    public function courses(CourseRepository $courseRepository, SerializerInterface $serializer): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->serializeCollection($serializer, $courseRepository->findAllForAdminRead(), 'classe:read');
    }

    #[Route('/activities', name: 'api_admin_activities', methods: ['GET'])]
    public function activities(ActivityRepository $activityRepository, SerializerInterface $serializer): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->serializeCollection($serializer, $activityRepository->findAllForAdminRead(), 'classe:read');
    }

    private function serializeCollection(
        SerializerInterface $serializer,
        array $items,
        string $group
    ): JsonResponse {
        try {
            $json = $serializer->serialize($items, 'json', ['groups' => $group]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur de sérialisation: ' . $e->getMessage()], 500);
        }

        return new JsonResponse($json, 200, [], true);
    }
}