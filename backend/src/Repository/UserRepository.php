<?php

namespace App\Repository;

use App\Entity\Parents;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function findOneForApiRead(int $id, string $readGroup): ?User
    {
        if ($readGroup === 'student:read') {
            return $this->getEntityManager()->createQueryBuilder()
                ->select('DISTINCT student', 'classes', 'courses', 'activities', 'progresses')
                ->from(Student::class, 'student')
                ->leftJoin('student.classes', 'classes')
                ->leftJoin('classes.courses', 'courses')
                ->leftJoin('courses.activities', 'activities')
                ->leftJoin('courses.progresses', 'progresses')
                ->andWhere('student.id = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->getOneOrNullResult();
        }

        if ($readGroup === 'teacher:read') {
            return $this->getEntityManager()->createQueryBuilder()
                ->select('DISTINCT teacher', 'teachingClasses', 'students', 'courses', 'activities', 'progresses', 'progressStudent')
                ->from(Teacher::class, 'teacher')
                ->leftJoin('teacher.teachingClasses', 'teachingClasses')
                ->leftJoin('teachingClasses.students', 'students')
                ->leftJoin('teachingClasses.courses', 'courses')
                ->leftJoin('courses.activities', 'activities')
                ->leftJoin('courses.progresses', 'progresses')
                ->leftJoin('progresses.student', 'progressStudent')
                ->andWhere('teacher.id = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->getOneOrNullResult();
        }

        if ($readGroup === 'parent:read') {
            return $this->getEntityManager()->createQueryBuilder()
                ->select('DISTINCT parent', 'children')
                ->from(Parents::class, 'parent')
                ->leftJoin('parent.children', 'children')
                ->andWhere('parent.id = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->getOneOrNullResult();
        }

        return $this->find($id);
    }

    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
