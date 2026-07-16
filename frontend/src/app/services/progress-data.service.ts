import { Injectable } from '@angular/core';
import { AcademicProgressCard, AcademicUser } from '../models/academic.model';
import { AcademicStructureService } from './academic-structure.service';

@Injectable({
  providedIn: 'root',
})
export class ProgressDataService {
  constructor(private academicStructureService: AcademicStructureService) {}

  getProgressCards(userInfo: AcademicUser): AcademicProgressCard[] {
    return this.academicStructureService.getClasses(userInfo).flatMap((academicClass) =>
      (academicClass.courses ?? []).flatMap((course) =>
        (course.progresses ?? []).map((progress) => ({
          ...progress,
          courseTitle: course.courseTitle,
          className: academicClass.className,
        }))
      )
    );
  }
}