<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Entity\Activity;
use App\Entity\Attachment;
use App\Entity\Classe;
use App\Entity\Course;
use App\Entity\Message;
use App\Entity\Notification;
use App\Entity\Progress;
use App\Entity\Report;
use App\Entity\Student;
use App\Entity\User;

final class EntityIntegrationTest extends BaseKernel
{
    public function testPersistUser(): void
    {
        $user = new User();
        $user->setEmail('integration-user@example.com');
        $user->setPassword('secret');
        $user->setLastName('Test');
        $user->setFirstName('Integration');
        $user->setRoles(['ROLE_USER']);

        $this->em->persist($user);
        $this->em->flush();

        self::assertNotNull($user->getId());
        self::assertSame('integration-user@example.com', $user->getEmail());
    }

    public function testPersistClasseAndCourse(): void
    {
        $classe = new Classe();
        $classe->setClassName('Integration Class');
        $classe->setClassDescription('Classe utilisée pour test intégration');

        $course = new Course();
        $course->setCourseTitle('Integration Course');
        $course->setCourseDescription('Description intégration');

        $classe->addCourse($course);

        $this->em->persist($classe);
        $this->em->persist($course);
        $this->em->flush();

        self::assertNotNull($classe->getId());
        self::assertNotNull($course->getId());
        self::assertSame(1, $classe->getCourses()->count());
        self::assertSame($classe, $course->getClasse());
    }

    public function testPersistStudentAndProgress(): void
    {
        $student = new Student();
        $student->setEmail('integration-student@example.com');
        $student->setPassword('secret');
        $student->setLastName('Student');
        $student->setFirstName('Integration');
        $student->setRoles(['ROLE_USER']);
        $student->setEnrollmentDate(new \DateTime('2024-01-01'));

        $course = new Course();
        $course->setCourseTitle('Progress Course');
        $course->setCourseDescription('Description de progression');

        $progress = new Progress();
        $progress->setProgressPercent(42.0);
        $progress->setProgressGrade('B');

        $student->addProgress($progress);
        $course->addProgress($progress);

        $this->em->persist($student);
        $this->em->persist($course);
        $this->em->persist($progress);
        $this->em->flush();

        self::assertNotNull($progress->getId());
        self::assertSame(1, $student->getProgresses()->count());
        self::assertSame(1, $course->getProgresses()->count());
    }

    public function testPersistActivity(): void
    {
        $course = new Course();
        $course->setCourseTitle('Activity Course');
        $course->setCourseDescription('Description activity');

        $activity = new Activity();
        $activity->setActivityType('Lecture');
        $activity->setActivityTitle('Integration Activity');
        $activity->setActivityDescription('Activité de test');
        $activity->setActivityDate(new \DateTimeImmutable('2024-02-01'));

        $course->addActivity($activity);

        $this->em->persist($course);
        $this->em->persist($activity);
        $this->em->flush();

        self::assertNotNull($activity->getId());
        self::assertSame(1, $course->getActivities()->count());
        self::assertSame($course, $activity->getCourse());
    }

    public function testPersistMessageAndAttachment(): void
    {
        $sender = new User();
        $sender->setEmail('integration-sender@example.com');
        $sender->setPassword('secret');
        $sender->setLastName('Sender');
        $sender->setFirstName('Integration');
        $sender->setRoles(['ROLE_USER']);

        $receiver = new User();
        $receiver->setEmail('integration-receiver@example.com');
        $receiver->setPassword('secret');
        $receiver->setLastName('Receiver');
        $receiver->setFirstName('Integration');
        $receiver->setRoles(['ROLE_USER']);

        $message = new Message();
        $message->setMessageContent('Test message');
        $message->setMessageSent(new \DateTimeImmutable('2024-03-01'));

        $sender->addSentMessage($message);
        $receiver->addReceivedMessage($message);

        $attachment = new Attachment();
        $attachment->setAttachmentName('file.txt');
        $attachment->setAttachmentPath('/tmp/file.txt');
        $attachment->setAttachmentType('text/plain');
        $attachment->setAttachmentSize(100);
        $attachment->setAttachmentCreatedAt(new \DateTimeImmutable('2024-03-01'));

        $message->addAttachment($attachment);

        $this->em->persist($sender);
        $this->em->persist($receiver);
        $this->em->persist($message);
        $this->em->persist($attachment);
        $this->em->flush();

        self::assertNotNull($message->getId());
        self::assertNotNull($attachment->getId());
        self::assertSame(1, $message->getAttachments()->count());
        self::assertSame($sender, $message->getSender());
        self::assertSame($receiver, $message->getReceiver());
    }

    public function testPersistNotification(): void
    {
        $user = new User();
        $user->setEmail('integration-notification@example.com');
        $user->setPassword('secret');
        $user->setLastName('Notify');
        $user->setFirstName('Integration');
        $user->setRoles(['ROLE_USER']);

        $notification = new Notification();
        $notification->setNotificationMessage('Un message de test');
        $notification->setNotificationDate(new \DateTimeImmutable('2024-04-01'));
        $notification->setNotificationLu(false);

        $user->addNotification($notification);

        $this->em->persist($user);
        $this->em->persist($notification);
        $this->em->flush();

        self::assertNotNull($notification->getId());
        self::assertSame(1, $user->getNotifications()->count());
        self::assertSame($user, $notification->getUser());
    }

    public function testPersistReportRelations(): void
    {
        $user = new User();
        $user->setEmail('integration-report@example.com');
        $user->setPassword('secret');
        $user->setLastName('Reporter');
        $user->setFirstName('Integration');
        $user->setRoles(['ROLE_USER']);

        $classe = new Classe();
        $classe->setClassName('Report Class');
        $classe->setClassDescription('Classe pour rapport');

        $course = new Course();
        $course->setCourseTitle('Report Course');
        $course->setCourseDescription('Description report');

        $classe->addCourse($course);

        $report = new Report();
        $report->setReportType('summary');
        $report->setReportData('Données de rapport');
        $report->setReportGeneratedAt(new \DateTimeImmutable('2024-05-01'));

        $classe->addReport($report);
        $user->addReport($report);
        $report->setCourse($course);

        $this->em->persist($user);
        $this->em->persist($classe);
        $this->em->persist($course);
        $this->em->persist($report);
        $this->em->flush();

        self::assertNotNull($report->getId());
        self::assertSame(1, $classe->getReports()->count());
        self::assertSame(1, $user->getReports()->count());
        self::assertSame($course, $report->getCourse());
    }
}
