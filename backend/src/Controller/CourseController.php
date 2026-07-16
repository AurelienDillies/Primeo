<?php

namespace App\Controller;

use App\Repository\ActivityRepository;
use App\Repository\CourseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/courses')]
class CourseController extends AbstractController
{
    #[Route('/{id}/activities', name: 'get_course_activities', methods: ['GET'])]
    public function getCourseActivities(
        int $id,
        CourseRepository $courseRepository,
        ActivityRepository $activityRepository,
        SerializerInterface $serializer
    ): JsonResponse {
        if (
            !$this->isGranted('ROLE_ADMIN')
            && !$this->isGranted('ROLE_TEACHER')
            && !$this->isGranted('ROLE_STUDENT')
        ) {
            throw $this->createAccessDeniedException('Acces refuse.');
        }

        $course = $courseRepository->find($id);
        if (!$course) {
            return new JsonResponse(['error' => 'Cours non trouve'], 404);
        }

        try {
            $json = $serializer->serialize(
                $activityRepository->findByCourseIdForRead($id),
                'json',
                ['groups' => 'classe:read']
            );
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur de serialisation: ' . $e->getMessage()], 500);
        }

        return new JsonResponse($json, 200, [], true);
    }
}
