<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Teacher;
use App\Entity\Student;
use App\Entity\Classe;
use App\Entity\Course;
use App\Entity\Activity;
use App\Entity\Progress;
use App\Entity\Message;
use App\Entity\Attachment;
use App\Entity\Notification;
use App\Entity\Parents;
use App\Entity\Report;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        // ===== USERS =====
        $user_admin = new User();
        $user_admin->setEmail('admin@primeo.com');
        $user_admin->setPassword(password_hash('admin', PASSWORD_BCRYPT));
        $user_admin->setLastName('Doe');
        $user_admin->setFirstName('John');
        $user_admin->setRoles(['ROLE_ADMIN']);

        $user_teacher = new Teacher();
        $user_teacher->setEmail('teacher@primeo.com');
        $user_teacher->setPassword(password_hash('teacher', PASSWORD_BCRYPT));
        $user_teacher->setLastName('Smith');
        $user_teacher->setFirstName('Jane');
        $user_teacher->setRoles(['ROLE_TEACHER']);
        $user_teacher->setSubject('Mathématiques');

        $user_teacher2 = new Teacher();
        $user_teacher2->setEmail('teacher2@primeo.com');
        $user_teacher2->setPassword(password_hash('teacher2', PASSWORD_BCRYPT));
        $user_teacher2->setLastName('Johnson');
        $user_teacher2->setFirstName('Robert');
        $user_teacher2->setRoles(['ROLE_TEACHER']);
        $user_teacher2->setSubject('Français');

        $user_student = new Student();
        $user_student->setEmail('student@primeo.com');
        $user_student->setPassword(password_hash('student', PASSWORD_BCRYPT));
        $user_student->setLastName('Brown');
        $user_student->setFirstName('Emily');
        $user_student->setRoles(['ROLE_STUDENT']);

        $user_student2 = new Student();
        $user_student2->setEmail('student2@primeo.com');
        $user_student2->setPassword(password_hash('student2', PASSWORD_BCRYPT));
        $user_student2->setLastName('Garcia');
        $user_student2->setFirstName('Carlos');
        $user_student2->setRoles(['ROLE_STUDENT']);

        $user_student3 = new Student();
        $user_student3->setEmail('student3@primeo.com');
        $user_student3->setPassword(password_hash('student3', PASSWORD_BCRYPT));
        $user_student3->setLastName('Martin');
        $user_student3->setFirstName('Sophie');
        $user_student3->setRoles(['ROLE_STUDENT']);

        $user_parent = new Parents();  
        $user_parent->setEmail('parent@primeo.com');
        $user_parent->setPassword(password_hash('parent', PASSWORD_BCRYPT));
        $user_parent->setLastName('Wilson');
        $user_parent->setFirstName('Michael');
        $user_parent->setRoles(['ROLE_PARENT']);
        $user_parent->addChild($user_student);
        $user_parent->addChild($user_student2);

        $user_parent2 = new Parents();  
        $user_parent2->setEmail('parent2@primeo.com');
        $user_parent2->setPassword(password_hash('parent2', PASSWORD_BCRYPT));
        $user_parent2->setLastName('Davis');
        $user_parent2->setFirstName('Lisa');
        $user_parent2->setRoles(['ROLE_PARENT']);
        $user_parent2->addChild($user_student3);

        $manager->persist($user_admin);
        $manager->persist($user_teacher);
        $manager->persist($user_teacher2);
        $manager->persist($user_student);
        $manager->persist($user_student2);
        $manager->persist($user_student3);
        $manager->persist($user_parent);
        $manager->persist($user_parent2);

        // ===== CLASSES =====
        $classe1 = new Classe();
        $classe1->setClassName('Classe 10A');
        $classe1->setClassDescription('Classe de mathématiques et sciences niveau 10');
        $classe1->setTeacher($user_teacher);
        $user_teacher->addTeachingClass($classe1);
        $classe1->addStudent($user_student);
        $classe1->addStudent($user_student2);
        $classe1->addStudent($user_student3);
        $user_student->addClass($classe1);
        $user_student2->addClass($classe1);
        $user_student3->addClass($classe1);

        $classe2 = new Classe();
        $classe2->setClassName('Classe 11B');
        $classe2->setClassDescription('Classe de littérature et histoire niveau 11');
        $classe2->setTeacher($user_teacher2);
        $classe2->addStudent($user_student);
        $classe2->addStudent($user_student2);
        $user_teacher2->addTeachingClass($classe2);
        $classe2->addStudent($user_student);
        $classe2->addStudent($user_student2);
        $user_student->addClass($classe2);
        $user_student2->addClass($classe2);

        $manager->persist($classe1);
        $manager->persist($classe2);
        $manager->persist($user_teacher);
        $manager->persist($user_teacher2);
        $manager->persist($user_student);
        $manager->persist($user_student2);
        $manager->persist($user_student3);

        // ===== COURSES =====
        $course1 = new Course();
        $course1->setCourseTitle('Mathématiques Avancées');
        $course1->setCourseDescription('Cours sur l\'algèbre linéaire et le calcul avancé');
        $course1->setCourseResourcefile('math_resources.pdf');
        $course1->setCourseVideoUrl('https://example.com/math-video');
        $course1->setClasse($classe1);

        $course2 = new Course();
        $course2->setCourseTitle('Biologie Générale');
        $course2->setCourseDescription('Introduction à la biologie cellulaire et l\'écologie');
        $course2->setCourseResourcefile('biology_resources.pdf');
        $course2->setCourseVideoUrl('https://example.com/biology-video');
        $course2->setClasse($classe1);

        $course3 = new Course();
        $course3->setCourseTitle('Histoire Mondiale');
        $course3->setCourseDescription('Histoire des civilisations du Moyen-Âge à nos jours');
        $course3->setCourseResourcefile('history_resources.pdf');
        $course3->setCourseVideoUrl('https://example.com/history-video');
        $course3->setClasse($classe2);

        $course4 = new Course();
        $course4->setCourseTitle('Littérature Française');
        $course4->setCourseDescription('Étude des classiques de la littérature française');
        $course4->setCourseResourcefile('literature_resources.pdf');
        $course4->setCourseVideoUrl('https://example.com/literature-video');
        $course4->setClasse($classe2);

        $manager->persist($course1);
        $manager->persist($course2);
        $manager->persist($course3);
        $manager->persist($course4);

        // ===== ACTIVITIES =====
        $activity1 = new Activity();
        $activity1->setActivityType('Quiz');
        $activity1->setActivityTitle('Quiz Algèbre Linéaire');
        $activity1->setActivityDescription('Quiz sur les vecteurs et matrices');
        $activity1->setActivityDate(new \DateTimeImmutable('2026-05-10'));
        $activity1->setCourse($course1);

        $activity2 = new Activity();
        $activity2->setActivityType('Devoir');
        $activity2->setActivityTitle('Devoir Biologie');
        $activity2->setActivityDescription('Étude comparative des écosystèmes');
        $activity2->setActivityDate(new \DateTimeImmutable('2026-05-15'));
        $activity2->setCourse($course2);

        $activity3 = new Activity();
        $activity3->setActivityType('Examen');
        $activity3->setActivityTitle('Examen Histoire');
        $activity3->setActivityDescription('Examen final du cours d\'histoire mondiale');
        $activity3->setActivityDate(new \DateTimeImmutable('2026-05-20'));
        $activity3->setCourse($course3);

        $activity4 = new Activity();
        $activity4->setActivityType('Projet');
        $activity4->setActivityTitle('Projet Littéraire');
        $activity4->setActivityDescription('Analyse comparative de deux œuvres littéraires');
        $activity4->setActivityDate(new \DateTimeImmutable('2026-05-25'));
        $activity4->setCourse($course4);

        $manager->persist($activity1);
        $manager->persist($activity2);
        $manager->persist($activity3);
        $manager->persist($activity4);

        // ===== PROGRESS =====
        $progress1 = new Progress();
        $progress1->setStudent($user_student);
        $progress1->setCourse($course1);
        $progress1->setProgressPercent(75.5);
        $progress1->setProgressGrade('B+');

        $progress2 = new Progress();
        $progress2->setStudent($user_student);
        $progress2->setCourse($course2);
        $progress2->setProgressPercent(82.0);
        $progress2->setProgressGrade('A-');

        $progress3 = new Progress();
        $progress3->setStudent($user_student2);
        $progress3->setCourse($course1);
        $progress3->setProgressPercent(65.0);
        $progress3->setProgressGrade('C+');

        $progress4 = new Progress();
        $progress4->setStudent($user_student2);
        $progress4->setCourse($course2);
        $progress4->setProgressPercent(70.5);
        $progress4->setProgressGrade('B');

        $progress5 = new Progress();
        $progress5->setStudent($user_student3);
        $progress5->setCourse($course1);
        $progress5->setProgressPercent(88.0);
        $progress5->setProgressGrade('A');

        $progress6 = new Progress();
        $progress6->setStudent($user_student);
        $progress6->setCourse($course3);
        $progress6->setProgressPercent(78.0);
        $progress6->setProgressGrade('B+');

        $progress7 = new Progress();
        $progress7->setStudent($user_student2);
        $progress7->setCourse($course3);
        $progress7->setProgressPercent(85.0);
        $progress7->setProgressGrade('A-');

        $manager->persist($progress1);
        $manager->persist($progress2);
        $manager->persist($progress3);
        $manager->persist($progress4);
        $manager->persist($progress5);
        $manager->persist($progress6);
        $manager->persist($progress7);

        // ===== MESSAGES =====
        $message1 = new Message();
        $message1->setMessageContent('Bonjour, avez-vous des questions sur le dernier cours ?');
        $message1->setMessageSent(new \DateTimeImmutable('2026-05-01'));
        $message1->setSender($user_teacher);
        $message1->setReceiver($user_student);

        $message2 = new Message();
        $message2->setMessageContent('Merci pour votre aide, j\'ai bien compris maintenant.');
        $message2->setMessageSent(new \DateTimeImmutable('2026-05-01 10:30'));
        $message2->setSender($user_student);
        $message2->setReceiver($user_teacher);

        $message3 = new Message();
        $message3->setMessageContent('Pouvez-vous vérifier mon devoir avant la date limite ?');
        $message3->setMessageSent(new \DateTimeImmutable('2026-05-02'));
        $message3->setSender($user_student2);
        $message3->setReceiver($user_teacher);

        $message4 = new Message();
        $message4->setMessageContent('Bien sûr, je l\'examinerai ce soir.');
        $message4->setMessageSent(new \DateTimeImmutable('2026-05-02 15:00'));
        $message4->setSender($user_teacher);
        $message4->setReceiver($user_student2);

        $message5 = new Message();
        $message5->setMessageContent('Salut ! Tu as bien compris le dernier projet ?');
        $message5->setMessageSent(new \DateTimeImmutable('2026-05-03'));
        $message5->setSender($user_student);
        $message5->setReceiver($user_student2);

        $manager->persist($message1);
        $manager->persist($message2);
        $manager->persist($message3);
        $manager->persist($message4);
        $manager->persist($message5);

        // ===== ATTACHMENTS =====
        $attachment1 = new Attachment();
        $attachment1->setAttachmentName('cours_algebre.pdf');
        $attachment1->setAttachmentPath('/uploads/documents/cours_algebre.pdf');
        $attachment1->setAttachmentType('pdf');
        $attachment1->setAttachmentSize(2048576);
        $attachment1->setAttachmentCreatedAt(new \DateTimeImmutable('2026-05-01'));
        $attachment1->setMessage($message1);

        $attachment2 = new Attachment();
        $attachment2->setAttachmentName('devoir_biologie.docx');
        $attachment2->setAttachmentPath('/uploads/documents/devoir_biologie.docx');
        $attachment2->setAttachmentType('docx');
        $attachment2->setAttachmentSize(512000);
        $attachment2->setAttachmentCreatedAt(new \DateTimeImmutable('2026-05-02'));
        $attachment2->setMessage($message3);

        $attachment3 = new Attachment();
        $attachment3->setAttachmentName('correction_devoir.pdf');
        $attachment3->setAttachmentPath('/uploads/documents/correction_devoir.pdf');
        $attachment3->setAttachmentType('pdf');
        $attachment3->setAttachmentSize(1024000);
        $attachment3->setAttachmentCreatedAt(new \DateTimeImmutable('2026-05-02 16:00'));
        $attachment3->setMessage($message4);

        $manager->persist($attachment1);
        $manager->persist($attachment2);
        $manager->persist($attachment3);

        // ===== NOTIFICATIONS =====
        $notification1 = new Notification();
        $notification1->setNotificationMessage('Vous avez reçu une note pour le devoir de mathématiques');
        $notification1->setNotificationDate(new \DateTimeImmutable('2026-05-05'));
        $notification1->setNotificationLu(true);
        $notification1->setUser($user_student);

        $notification2 = new Notification();
        $notification2->setNotificationMessage('Nouveau cours disponible : Algèbre Avancée');
        $notification2->setNotificationDate(new \DateTimeImmutable('2026-05-06'));
        $notification2->setNotificationLu(false);
        $notification2->setUser($user_student);

        $notification3 = new Notification();
        $notification3->setNotificationMessage('Rappel : Date limite du projet approche');
        $notification3->setNotificationDate(new \DateTimeImmutable('2026-05-07'));
        $notification3->setNotificationLu(false);
        $notification3->setUser($user_student2);

        $notification4 = new Notification();
        $notification4->setNotificationMessage('Vous avez un nouveau message de votre professeur');
        $notification4->setNotificationDate(new \DateTimeImmutable('2026-05-08'));
        $notification4->setNotificationLu(true);
        $notification4->setUser($user_student3);

        $notification5 = new Notification();
        $notification5->setNotificationMessage('Bienvenue sur Priméo');
        $notification5->setNotificationDate(new \DateTimeImmutable('2026-05-01'));
        $notification5->setNotificationLu(true);
        $notification5->setUser($user_parent);

        $manager->persist($notification1);
        $manager->persist($notification2);
        $manager->persist($notification3);
        $manager->persist($notification4);
        $manager->persist($notification5);

        // ===== REPORTS =====
        $report1 = new Report();
        $report1->setReportType('Rapport de Performance de Classe');
        $report1->setReportData(json_encode([
            'total_students' => 3,
            'average_grade' => '78.5%',
            'top_performer' => 'Emily Brown',
            'needs_improvement' => 'Carlos Garcia'
        ]));
        $report1->setReportGeneratedAt(new \DateTimeImmutable('2026-05-10'));
        $report1->setClasse($classe1);
        $report1->setGeneratedBy($user_teacher);

        $report2 = new Report();
        $report2->setReportType('Rapport de Performance de Cours');
        $report2->setReportData(json_encode([
            'course' => 'Mathématiques Avancées',
            'enrollment' => 3,
            'completion_rate' => '85%',
            'average_score' => '75.17%'
        ]));
        $report2->setReportGeneratedAt(new \DateTimeImmutable('2026-05-11'));
        $report2->setCourse($course1);
        $report2->setGeneratedBy($user_teacher);

        $report3 = new Report();
        $report3->setReportType('Rapport de Performance de Classe');
        $report3->setReportData(json_encode([
            'total_students' => 2,
            'average_grade' => '81.5%',
            'top_performer' => 'Carlos Garcia',
            'needs_improvement' => 'Emily Brown'
        ]));
        $report3->setReportGeneratedAt(new \DateTimeImmutable('2026-05-12'));
        $report3->setClasse($classe2);
        $report3->setGeneratedBy($user_teacher2);

        $report4 = new Report();
        $report4->setReportType('Rapport de Performance de Cours');
        $report4->setReportData(json_encode([
            'course' => 'Histoire Mondiale',
            'enrollment' => 2,
            'completion_rate' => '90%',
            'average_score' => '81.5%'
        ]));
        $report4->setReportGeneratedAt(new \DateTimeImmutable('2026-05-13'));
        $report4->setCourse($course3);
        $report4->setGeneratedBy($user_teacher2);

        $manager->persist($report1);
        $manager->persist($report2);
        $manager->persist($report3);
        $manager->persist($report4);

        // Flush all data
        $manager->flush();
    }

}
