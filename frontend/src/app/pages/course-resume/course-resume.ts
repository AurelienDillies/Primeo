import { CommonModule, Location } from '@angular/common';
import { Component, inject } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Observable, of } from 'rxjs';
import { catchError, map, switchMap } from 'rxjs/operators';
import { Activitie } from '../../components/activitie/activitie';
import { AcademicActivity } from '../../models/academic.model';
import { CourseDataService } from '../../services/course-data.service';

type CourseNavigationContext = {
  from: 'cours' | 'classe-resume';
  classId?: number;
};

type CourseResumeVM = {
  courseId: number | null;
  activities: AcademicActivity[];
};

@Component({
  selector: 'app-course-resume',
  imports: [CommonModule, Activitie],
  templateUrl: './course-resume.html',
  styleUrl: './course-resume.css',
})
export class CourseResume {
  vm$: Observable<CourseResumeVM>;
  private readonly route = inject(ActivatedRoute);
  private readonly courseDataService = inject(CourseDataService);
  private readonly location = inject(Location);
  private readonly router = inject(Router);

  constructor() {
    this.vm$ = this.route.paramMap.pipe(
      map((params) => {
        const rawId = params.get('courseId');
        return rawId ? Number(rawId) : null;
      }),
      switchMap((courseId) => {
        if (!courseId || Number.isNaN(courseId)) {
          return of<CourseResumeVM>({ courseId: null, activities: [] });
        }

        return this.courseDataService.getActivitiesByCourseId(courseId).pipe(
          map((activities): CourseResumeVM => ({ courseId, activities })),
          catchError(() => of<CourseResumeVM>({ courseId, activities: [] }))
        );
      })
    );
  }

  private get context(): CourseNavigationContext {
    const state = history.state as Partial<CourseNavigationContext>;

    if (state.from === 'classe-resume' && typeof state.classId === 'number') {
      return { from: 'classe-resume', classId: state.classId };
    }

    if (state.from === 'cours') {
      return { from: 'cours' };
    }

    const sourceFrom = this.route.snapshot.queryParamMap.get('from');
    const rawClassId = this.route.snapshot.queryParamMap.get('classId');
    const parsedClassId = rawClassId ? Number(rawClassId) : NaN;

    if (sourceFrom === 'classe-resume' && !Number.isNaN(parsedClassId)) {
      return { from: 'classe-resume', classId: parsedClassId };
    }

    return { from: 'cours' };
  }

  get parentFromForActivities(): 'cours' | 'classe-resume' {
    return this.context.from;
  }

  get parentClassIdForActivities(): number | undefined {
    return this.context.classId;
  }

  goBack(): void {
    if (this.context.from === 'classe-resume' && this.context.classId) {
      this.router.navigate(['/resume-classes', this.context.classId]);
      return;
    }

    if (this.context.from === 'cours') {
      this.router.navigate(['/cours']);
      return;
    }

    if (window.history.length > 1) {
      this.location.back();
      return;
    }

    this.router.navigate(['/cours']);
  }
}
