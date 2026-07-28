<?php

namespace App\Tests\Unit\Tests;

use PHPUnit\Framework\TestCase;
use App\Entity\Activity;
use App\Entity\Classe;
use App\Entity\Course;
use App\Entity\Student;
use App\Service\Calculates;
use Doctrine\Common\Collections\ArrayCollection;


class CalculatesTest extends TestCase

{
    public function testCalculatePourcentCourseParRapportAuxActivities(): void
    {
        $activity1 = $this->createMock(Activity::class);
        $activity1->method('getPourcent')->willReturn('20');

        $activity2 = $this->createMock(Activity::class);
        $activity2->method('getPourcent')->willReturn('30');

        $activity3 = $this->createMock(Activity::class);
        $activity3->method('getPourcent')->willReturn('15');

        $course = $this->createMock(Course::class);
        $course->method('getActivities')->willReturn(
            new ArrayCollection([$activity1, $activity2, $activity3])
        );

        $calculates = new Calculates();

        $result = $calculates->calculatePourcentCourse($course);

        $this->assertSame('65.00', $result);
    }

    public function testCalculatePourcentStudentParRapportAuxCourses(): void
    {
        // Course 1 : 2 activités
        $activity1 = $this->createMock(Activity::class);
        $activity1->method('getPourcent')->willReturn('10');

        $activity2 = $this->createMock(Activity::class);
        $activity2->method('getPourcent')->willReturn('10');

        $course1 = $this->createMock(Course::class);
        $course1->method('getActivities')->willReturn(
            new ArrayCollection([$activity1, $activity2])
        );

        // Course 2 : 1 activité
        $activity3 = $this->createMock(Activity::class);
        $activity3->method('getPourcent')->willReturn('40');

        $course2 = $this->createMock(Course::class);
        $course2->method('getActivities')->willReturn(
            new ArrayCollection([$activity3])
        );

        // Une seule Classe regroupant les 2 Course
        $classe = $this->createMock(Classe::class);
        $classe->method('getCourses')->willReturn(
            new ArrayCollection([$course1, $course2])
        );

        $student = $this->createMock(Student::class);
        $student->method('getClasses')->willReturn(
            new ArrayCollection([$classe])
        );
        $calculates = new Calculates();

        $result = $calculates->calculatePourcentStudent($student);

        $this->assertSame('60.00', $result);
    }
}