<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\Course;
use App\Repository\ActivityRepository;
use App\Repository\ClasseRepository;
use App\Repository\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
class CourseController extends AbstractController
{
    #[Route('/courses/{id}', name: 'get_course', methods: ['GET'])]
    public function getCourse(int $id, CourseRepository $courseRepository, SerializerInterface $serializer): JsonResponse
    {
        $course = $courseRepository->find($id);
        if (!$course) {
            return new JsonResponse(['error' => 'Cours non trouvé.'], 404);
        }

        $payload = json_decode($serializer->serialize($course, 'json', ['groups' => 'classe:read']), true);
        $payload['classId'] = $course->getClasse()?->getId();

        return new JsonResponse($payload);
    }

    #[Route('/courses/{id}/activities', name: 'get_course_activities', methods: ['GET'])]
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

    #[Route('/activities/{id}', name: 'get_activity', methods: ['GET'])]
    public function getActivity(int $id, ActivityRepository $activityRepository, SerializerInterface $serializer): JsonResponse
    {
        $activity = $activityRepository->find($id);
        if (!$activity) {
            return new JsonResponse(['error' => 'Activité non trouvée.'], 404);
        }
        if ($this->isGranted('ROLE_STUDENT')) {
            $classe = $activity->getCourse()?->getClasse();
            if (!$classe || !$classe->getStudents()->contains($this->getUser())) {
                throw $this->createAccessDeniedException('Cette activité ne vous est pas accessible.');
            }
        }

        $payload = json_decode($serializer->serialize($activity, 'json', ['groups' => 'classe:read']), true);
        $payload['courseId'] = $activity->getCourse()?->getId();

        return new JsonResponse($payload);
    }

    #[Route('/courses', name: 'create_course', methods: ['POST'])]
    public function createCourse(
        Request $request,
        ClasseRepository $classeRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $this->denyAcademicWriteAccess();
        $data = $this->decodePayload($request);
        if ($data === null) {
            return new JsonResponse(['error' => 'Payload JSON invalide.'], 400);
        }

        $course = new Course();
        $error = $this->applyCourseData($course, $data, $classeRepository, true);
        if ($error) {
            return new JsonResponse(['error' => $error], 422);
        }

        $entityManager->persist($course);
        $entityManager->flush();

        return new JsonResponse(['id' => $course->getId(), 'message' => 'Cours créé avec succès.'], 201);
    }

    #[Route('/courses/{id}', name: 'update_course', methods: ['PUT'])]
    public function updateCourse(
        int $id,
        Request $request,
        CourseRepository $courseRepository,
        ClasseRepository $classeRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $this->denyAcademicWriteAccess();
        $course = $courseRepository->find($id);
        if (!$course) {
            return new JsonResponse(['error' => 'Cours non trouvé.'], 404);
        }
        $classe = $course->getClasse();
        if ($this->isGranted('ROLE_STUDENT') && (!$classe || !$classe->getStudents()->contains($this->getUser()))) {
            throw $this->createAccessDeniedException('Ce cours ne vous est pas accessible.');
        }

        $data = $this->decodePayload($request);
        if ($data === null) {
            return new JsonResponse(['error' => 'Payload JSON invalide.'], 400);
        }

        $error = $this->applyCourseData($course, $data, $classeRepository, false);
        if ($error) {
            return new JsonResponse(['error' => $error], 422);
        }

        $entityManager->flush();

        return new JsonResponse(['message' => 'Cours mis à jour avec succès.']);
    }

    #[Route('/activities', name: 'create_activity', methods: ['POST'])]
    public function createActivity(
        Request $request,
        CourseRepository $courseRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $this->denyAcademicWriteAccess();
        $data = $this->decodePayload($request);
        if ($data === null) {
            return new JsonResponse(['error' => 'Payload JSON invalide.'], 400);
        }

        $activity = new Activity();
        $error = $this->applyActivityData($activity, $data, $courseRepository, true);
        if ($error) {
            return new JsonResponse(['error' => $error], 422);
        }

        $entityManager->persist($activity);
        $entityManager->flush();

        return new JsonResponse(['id' => $activity->getId(), 'message' => 'Activité créée avec succès.'], 201);
    }

    #[Route('/activities/{id}', name: 'update_activity', methods: ['PUT'])]
    public function updateActivity(
        int $id,
        Request $request,
        ActivityRepository $activityRepository,
        CourseRepository $courseRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $this->denyAcademicWriteAccess();
        $activity = $activityRepository->find($id);
        if (!$activity) {
            return new JsonResponse(['error' => 'Activité non trouvée.'], 404);
        }

        $data = $this->decodePayload($request);
        if ($data === null) {
            return new JsonResponse(['error' => 'Payload JSON invalide.'], 400);
        }

        $error = $this->applyActivityData($activity, $data, $courseRepository, false);
        if ($error) {
            return new JsonResponse(['error' => $error], 422);
        }

        $entityManager->flush();

        return new JsonResponse(['message' => 'Activité mise à jour avec succès.']);
    }

    #[Route('/courses/{id}', name: 'delete_course', methods: ['DELETE'])]
    public function deleteCourse(
        int $id,
        CourseRepository $courseRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $this->denyAcademicWriteAccess();
        $course = $courseRepository->find($id);
        if (!$course) {
            return new JsonResponse(['error' => 'Cours non trouvé.'], 404);
        }
        if ($this->isGranted('ROLE_STUDENT')) {
            $classe = $course->getClasse();
            if (!$classe || !$classe->getStudents()->contains($this->getUser())) {
                throw $this->createAccessDeniedException('Ce cours ne vous est pas accessible.');
            }
        }

        try {
            $entityManager->remove($course);
            $entityManager->flush();
        } catch (\Throwable $exception) {
            return new JsonResponse([
                'error' => 'Impossible de supprimer le cours : des données associées empêchent sa suppression.',
            ], 409);
        }

        return new JsonResponse(['message' => 'Cours supprimé avec succès.']);
    }

    #[Route('/activities/{id}', name: 'delete_activity', methods: ['DELETE'])]
    public function deleteActivity(
        int $id,
        ActivityRepository $activityRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $this->denyAcademicWriteAccess();
        $activity = $activityRepository->find($id);
        if (!$activity) {
            return new JsonResponse(['error' => 'Activité non trouvée.'], 404);
        }

        try {
            $entityManager->remove($activity);
            $entityManager->flush();
        } catch (\Throwable) {
            return new JsonResponse(['error' => 'Impossible de supprimer l’activité.'], 409);
        }

        return new JsonResponse(['message' => 'Activité supprimée avec succès.']);
    }

    private function decodePayload(Request $request): ?array
    {
        $data = json_decode($request->getContent(), true);

        return is_array($data) ? $data : null;
    }

    private function denyAcademicWriteAccess(): void
    {
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_TEACHER')) {
            throw $this->createAccessDeniedException('Accès refusé.');
        }
    }

    private function applyCourseData(Course $course, array $data, ClasseRepository $classeRepository, bool $required): ?string
    {
        if ($required && (!isset($data['courseTitle']) || trim((string) $data['courseTitle']) === '')) {
            return 'Le titre du cours est obligatoire.';
        }
        if ($required && (!isset($data['courseDescription']) || trim((string) $data['courseDescription']) === '')) {
            return 'La description du cours est obligatoire.';
        }
        if (array_key_exists('courseTitle', $data)) {
            $course->setCourseTitle(trim((string) $data['courseTitle']));
        }
        if (array_key_exists('courseDescription', $data)) {
            $course->setCourseDescription(trim((string) $data['courseDescription']));
        }
        foreach (['courseResourcefile', 'courseVideoUrl'] as $field) {
            if (array_key_exists($field, $data)) {
                $setter = 'set' . ucfirst($field);
                $course->{$setter}($data[$field] === null ? null : trim((string) $data[$field]));
            }
        }
        if (array_key_exists('classId', $data)) {
            $class = $classeRepository->find((int) $data['classId']);
            if (!$class) {
                return 'La classe sélectionnée est introuvable.';
            }
            $course->setClasse($class);
        } elseif ($required) {
            return 'La classe est obligatoire.';
        }

        return null;
    }

    private function applyActivityData(Activity $activity, array $data, CourseRepository $courseRepository, bool $required): ?string
    {
        foreach (['activityType', 'activityTitle'] as $field) {
            if ($required && (!isset($data[$field]) || trim((string) $data[$field]) === '')) {
                return 'Le type et le titre de l’activité sont obligatoires.';
            }
            if (array_key_exists($field, $data)) {
                $setter = 'set' . ucfirst($field);
                $activity->{$setter}(trim((string) $data[$field]));
            }
        }
        if (array_key_exists('activityDescription', $data)) {
            $activity->setActivityDescription($data['activityDescription'] === null ? null : trim((string) $data['activityDescription']));
        }
        if ($required && empty($data['activityDate'])) {
            return 'La date de l’activité est obligatoire.';
        }
        if (array_key_exists('activityDate', $data)) {
            try {
                $activity->setActivityDate(new \DateTimeImmutable((string) $data['activityDate']));
            } catch (\Exception) {
                return 'La date de l’activité est invalide.';
            }
        }
        if (array_key_exists('courseId', $data)) {
            $course = $courseRepository->find((int) $data['courseId']);
            if (!$course) {
                return 'Le cours sélectionné est introuvable.';
            }
            $activity->setCourse($course);
        } elseif ($required) {
            return 'Le cours est obligatoire.';
        }

        return null;
    }
}
