import { Injectable } from '@angular/core';
import { AcademicActivity, AcademicClass, AcademicCourse, AcademicUser } from '../models/academic.model';

@Injectable({
  providedIn: 'root',
})
export class AcademicStructureService {
  getClasses(userInfo: AcademicUser): AcademicClass[] {
    return userInfo.classes ?? userInfo.teachingClasses ?? [];
  }

  getCourses(userInfo: AcademicUser): AcademicCourse[] {
    return this.getClasses(userInfo).flatMap((academicClass) => academicClass.courses ?? []);
  }

  getActivities(userInfo: AcademicUser): AcademicActivity[] {
    return this.getCourses(userInfo).flatMap((course) => course.activities ?? []);
  }
}