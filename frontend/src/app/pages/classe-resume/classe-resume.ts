import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute } from '@angular/router';
import { catchError, map, switchMap } from 'rxjs/operators';
import { Observable, of } from 'rxjs';
import { Course } from '../../components/course/course';
import { AcademicCourse } from '../../models/academic.model';
import { ClassDataService } from '../../services/class-data.service';

type ClasseResumeVM = {
  classId: number | null;
  courses: AcademicCourse[];
};

@Component({
  selector: 'app-classe-resume',
  imports: [CommonModule, Course],
  templateUrl: './classe-resume.html',
  styleUrl: './classe-resume.css',
})
export class ClasseResume {
  vm$: Observable<ClasseResumeVM>;
  private readonly route = inject(ActivatedRoute);
  private readonly classDataService = inject(ClassDataService);

  constructor() {
    this.vm$ = this.route.paramMap.pipe(
      map((params) => {
        const rawId = params.get('classId');
        return rawId ? Number(rawId) : null;
      }),
      switchMap((classId) => {
        if (!classId || Number.isNaN(classId)) {
          return of({ classId: null, courses: [] });
        }

        return this.classDataService.getCoursesByClassId(classId).pipe(
          map((courses) => ({ classId, courses })),
          catchError(() => of({ classId, courses: [] }))
        );
      })
    );
  }
}