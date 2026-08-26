<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Parents;
use App\Entity\Progress;
use App\Entity\Student;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/parents')]
class ParentController extends AbstractController
{
    #[Route('/me/children', name: 'api_parent_children', methods: ['GET'])]
    public function children(): JsonResponse
    {
        $parent = $this->getAuthenticatedParent();
        if ($parent instanceof JsonResponse) {
            return $parent;
        }

        $children = [];
        foreach ($parent->getChildren() as $child) {
            $children[] = [
                'id' => $child->getId(),
                'first_name' => $child->getFirstName(),
                'last_name' => $child->getLastName(),
                'email' => $child->getEmail(),
                'enrollmentDate' => $child->getEnrollmentDate()?->format('Y-m-d'),
                'classesCount' => $child->getClasses()->count(),
                'progressCount' => $child->getProgresses()->count(),
            ];
        }

        return $this->json($children);
    }

    #[Route('/me/children/{id}', name: 'api_parent_child_detail', methods: ['GET'], requirements: ['id' => '\\d+'])]
    public function childDetail(int $id): JsonResponse
    {
        $parent = $this->getAuthenticatedParent();
        if ($parent instanceof JsonResponse) {
            return $parent;
        }

        $child = null;
        foreach ($parent->getChildren() as $candidate) {
            if ($candidate->getId() === $id) {
                $child = $candidate;
                break;
            }
        }

        if (!$child instanceof Student) {
            return $this->json(['error' => 'Enfant introuvable pour ce parent'], 404);
        }

        return $this->json($this->toChildPayload($child));
    }

    private function getAuthenticatedParent(): Parents|JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_PARENT');

        $user = $this->getUser();
        if (!$user instanceof Parents) {
            return $this->json(['error' => 'Utilisateur parent non authentifié'], 403);
        }

        return $user;
    }

    private function toChildPayload(Student $child): array
    {
        $classes = [];
        foreach ($child->getClasses() as $class) {
            $courses = [];
            foreach ($class->getCourses() as $course) {
                $courses[] = $this->toCoursePayload($course, $child);
            }

            $classes[] = [
                'id' => $class->getId(),
                'className' => $class->getClassName(),
                'classDescription' => $class->getClassDescription(),
                'courses' => $courses,
            ];
        }

        return [
            'id' => $child->getId(),
            'first_name' => $child->getFirstName(),
            'last_name' => $child->getLastName(),
            'email' => $child->getEmail(),
            'enrollmentDate' => $child->getEnrollmentDate()?->format('Y-m-d'),
            'roles' => $child->getRoles(),
            'classes' => $classes,
        ];
    }

    private function toCoursePayload(Course $course, Student $child): array
    {
        $activities = [];
        foreach ($course->getActivities() as $activity) {
            $activities[] = [
                'id' => $activity->getId(),
                'activityType' => $activity->getActivityType(),
                'activityTitle' => $activity->getActivityTitle(),
                'activityDescription' => $activity->getActivityDescription(),
                'activityDate' => $activity->getActivityDate()?->format(DATE_ATOM),
            ];
        }

        $progresses = [];
        foreach ($course->getProgresses() as $progress) {
            if ($progress->getStudent()?->getId() !== $child->getId()) {
                continue;
            }

            $progresses[] = $this->toProgressPayload($progress);
        }

        return [
            'id' => $course->getId(),
            'courseTitle' => $course->getCourseTitle(),
            'courseDescription' => $course->getCourseDescription(),
            'courseResourcefile' => $course->getCourseResourcefile(),
            'courseVideoUrl' => $course->getCourseVideoUrl(),
            'activities' => $activities,
            'progresses' => $progresses,
        ];
    }

    private function toProgressPayload(Progress $progress): array
    {
        return [
            'id' => $progress->getId(),
            'progressPercent' => $progress->getProgressPercent(),
            'progressGrade' => $progress->getProgressGrade(),
        ];
    }
}
