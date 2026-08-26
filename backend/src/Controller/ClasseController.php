<?php

namespace App\Controller;

use App\Entity\Classe;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Repository\ClasseRepository;
use App\Repository\CourseRepository;
use App\Repository\UserRepository;
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
        $this->denyStudentClassAccess($classe);

        try{
            $json = $serializer->serialize($classe, 'json', ['groups' => 'classe:read']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur de sérialisation: ' . $e->getMessage()], 500);
        }
        $payload = json_decode($json, true);
        $payload['studentIds'] = array_map(
            static fn (Student $student): int => (int) $student->getId(),
            $classe->getStudents()->toArray()
        );
        $payload['teacherId'] = $classe->getTeacher()?->getId();
        $payload['teacherName'] = $classe->getTeacher()
            ? trim($classe->getTeacher()->getFirstName() . ' ' . $classe->getTeacher()->getLastName())
            : null;
        return new JsonResponse($payload);
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
        $this->denyStudentClassAccess($classe);

        try {
            $json = $serializer->serialize($courseRepository->findByClasseIdForRead($id), 'json', ['groups' => 'classe:read']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur de sérialisation: ' . $e->getMessage()], 500);
        }

        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/', name: 'create_class', methods: ['POST'])]
    public function createClasse(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): JsonResponse
    {
        $this->denyClassCreationAccess();
        $data = $this->decodePayload($request);
        if ($data === null) {
            return new JsonResponse(['error' => 'Payload JSON invalide.'], 400);
        }

        $classe = new Classe();
        $error = $this->applyClassData($classe, $data, $userRepository, true, true);
        if ($error) {
            return new JsonResponse(['error' => $error], 422);
        }

        $this->applyStudents($classe, $data, $userRepository, $error);
        $entityManager->persist($classe);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Classe créée avec succès.', 'id' => $classe->getId()], 201);
    }

    #[Route('/{id}', name: 'update_class', methods: ['PUT'])]
    public function updateClasse(
        Request $request,
        EntityManagerInterface $entityManager,
        ClasseRepository $classeRepository,
        UserRepository $userRepository,
        int $id
    ): JsonResponse {
        $this->denyClassUpdateAccess();
        $classe = $classeRepository->find($id);
        if (!$classe) {
            return new JsonResponse(['error' => 'Classe non trouvée'], 404);
        }
        $data = $this->decodePayload($request);
        if ($data === null) {
            return new JsonResponse(['error' => 'Payload JSON invalide.'], 400);
        }
        if (array_key_exists('teacherId', $data) && !$this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse(['error' => 'Un enseignant ne peut pas modifier l’enseignant associé.'], 403);
        }

        $error = $this->applyClassData($classe, $data, $userRepository, false, $this->isGranted('ROLE_ADMIN'));
        if ($error) {
            return new JsonResponse(['error' => $error], 422);
        }
        $this->applyStudents($classe, $data, $userRepository, $error);
        if ($error) {
            return new JsonResponse(['error' => $error], 422);
        }

        $entityManager->flush();

        return new JsonResponse(['message' => 'Classe mise à jour avec succès.'], 200);
    }

    #[Route('/{id}', name: 'delete_class', methods: ['DELETE'])]
    public function deleteClasse(
        EntityManagerInterface $entityManager,
        ClasseRepository $classeRepository,
        int $id
    ): JsonResponse {
        $this->denyClassDeletionAccess();
        $classe = $classeRepository->find($id);
        if (!$classe) {
            return new JsonResponse(['error' => 'Classe non trouvée'], 404);
        }
        try {
            $entityManager->remove($classe);
            $entityManager->flush();
        } catch (\Throwable) {
            return new JsonResponse([
                'error' => 'Impossible de supprimer la classe : des données associées empêchent sa suppression.',
            ], 409);
        }

        return new JsonResponse(['message' => 'Classe supprimée avec succès.'], 200);
    }

    private function decodePayload(Request $request): ?array
    {
        $data = json_decode($request->getContent(), true);

        return is_array($data) ? $data : null;
    }

    private function applyClassData(
        Classe $classe,
        array $data,
        UserRepository $userRepository,
        bool $required,
        bool $allowTeacherChange
    ): ?string
    {
        if ($required && (!isset($data['className']) || trim((string) $data['className']) === '')) {
            return 'Le nom de la classe est obligatoire.';
        }

        if (array_key_exists('className', $data)) {
            $className = trim((string) $data['className']);
            if ($className === '') {
                return 'Le nom de la classe ne peut pas être vide.';
            }
            $classe->setClassName($className);
        }

        if (array_key_exists('classDescription', $data)) {
            $description = $data['classDescription'];
            $classe->setClassDescription($description === null ? null : trim((string) $description));
        }

        if (array_key_exists('teacherId', $data)) {
            if (!$allowTeacherChange) {
                return 'La modification de l’enseignant est interdite.';
            }
            if ($data['teacherId'] === null || $data['teacherId'] === '') {
                $classe->setTeacher(null);
            } else {
                if (!is_int($data['teacherId']) && !ctype_digit((string) $data['teacherId'])) {
                    return 'L’identifiant enseignant est invalide.';
                }
                $teacher = $userRepository->find((int) $data['teacherId']);
                if (!$teacher instanceof Teacher) {
                    return 'L’enseignant sélectionné est introuvable.';
                }
                $classe->setTeacher($teacher);
            }
        }

        if (array_key_exists('studentIds', $data)) {
            if (!is_array($data['studentIds'])) {
                return 'La liste des élèves est invalide.';
            }
            foreach ($data['studentIds'] as $studentId) {
                if (!is_int($studentId) && !ctype_digit((string) $studentId)) {
                    return 'Un identifiant élève est invalide.';
                }
                if (!$userRepository->find((int) $studentId) instanceof Student) {
                    return 'Un élève sélectionné est introuvable.';
                }
            }
        }

        return null;
    }

    private function applyStudents(Classe $classe, array $data, UserRepository $userRepository, ?string &$error): void
    {
        if ($error !== null || !array_key_exists('studentIds', $data)) {
            return;
        }

        $selectedIds = array_map('intval', $data['studentIds']);
        foreach ($classe->getStudents()->toArray() as $student) {
            if (!in_array($student->getId(), $selectedIds, true)) {
                $classe->removeStudent($student);
            }
        }
        foreach ($selectedIds as $studentId) {
            $student = $userRepository->find($studentId);
            if ($student instanceof Student) {
                $classe->addStudent($student);
            }
        }
    }

    private function denyStudentClassAccess(Classe $classe): void
    {
        if ($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_TEACHER')) {
            return;
        }

        $user = $this->getUser();
        if (!$user instanceof Student || !$classe->getStudents()->contains($user)) {
            throw $this->createAccessDeniedException('Cette classe ne vous est pas accessible.');
        }
    }

    private function denyClassCreationAccess(): void
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Seul un administrateur peut créer une classe.');
        }
    }

    private function denyClassUpdateAccess(): void
    {
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_TEACHER')) {
            throw $this->createAccessDeniedException('Seul un administrateur ou un enseignant peut modifier une classe.');
        }
    }

    private function denyClassDeletionAccess(): void
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Seul un administrateur peut supprimer une classe.');
        }
    }

}
