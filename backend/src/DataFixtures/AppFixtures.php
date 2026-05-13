<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{

    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable();
    }
    public function load(ObjectManager $manager): void
    {
        $user_admin = new User();
        $user_admin->setEmail('admin@digiforma.com');
        $user_admin->setPassword('admin');
        $user_admin->setLastName('Doe');
        $user_admin->setFirstName('John');
        $user_admin->setRoles(['ROLE_ADMIN']);
        $user_admin->setPassword(password_hash($user_admin->getPassword(), PASSWORD_BCRYPT));

        $user_teacher = new User();
        $user_teacher->setEmail('teacher@digiforma.com');
        $user_teacher->setPassword('teacher');
        $user_teacher->setLastName('Smith');
        $user_teacher->setFirstName('Jane');
        $user_teacher->setRoles(['ROLE_TEACHER']);
        $user_teacher->setPassword(password_hash($user_teacher->getPassword(), PASSWORD_BCRYPT));

        $user_student = new User();
        $user_student->setEmail('student@digiforma.com');
        $user_student->setPassword('student');
        $user_student->setLastName('Brown');
        $user_student->setFirstName('Emily');
        $user_student->setRoles(['ROLE_STUDENT']);
        $user_student->setPassword(password_hash($user_student->getPassword(), PASSWORD_BCRYPT));

        $user_parent = new User();  
        $user_parent->setEmail('parent@digiforma.com');
        $user_parent->setPassword('parent');
        $user_parent->setLastName('Wilson');
        $user_parent->setFirstName('Michael');
        $user_parent->setRoles(['ROLE_PARENT']);
        $user_parent->setPassword(password_hash($user_parent->getPassword(), PASSWORD_BCRYPT));


        $manager->persist($user_admin);
        $manager->persist($user_teacher);
        $manager->persist($user_student);
        $manager->persist($user_parent);
        $manager->flush();
    }
}
