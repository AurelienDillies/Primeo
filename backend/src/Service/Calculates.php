<?php

namespace App\Service;

use App\Entity\Course;
use App\Entity\Student;

final class Calculates
{
    public static function calculatePourcentCourse(Course $course): ?string
    {
        // Implementation for calculating percentage for a course
        $totalPourcent = 0.0;
        $activities = $course->getActivities();
        if ($activities->isEmpty()) {
            return null;
        }   
        foreach ($activities as $activity) {
            if ($activity->getPourcent() === null) {
                return null;
            }else {
                $totalPourcent += (float)$activity->getPourcent();
            }
        }
        return number_format($totalPourcent, 2); // Replace with actual calculation logic       
    }

    public static function calculatePourcentStudent(Student $student): ?string
    {
        // Implementation for calculating percentage for a student
        $totalPourcent = 0.0;
        $classes = $student->getClasses();
        if ($classes->isEmpty()) {
            return null;
        }

        foreach ($classes as $class) {
            $courses = $class->getCourses();
            if ($courses->isEmpty()) {
                return null;
            }

            foreach ($courses as $course) {
                $pourcent = self::calculatePourcentCourse($course);
                if ($pourcent === null) {
                    return null;
                }
                $totalPourcent += (float)$pourcent;
            }
        }

        return number_format($totalPourcent, 2); // Replace with actual calculation logic
    }
}
