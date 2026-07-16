import { Component, inject } from '@angular/core';
import { Location } from '@angular/common';
import { ActivatedRoute, Router } from '@angular/router';

type ActivityDetailsContext = {
  from: 'activites' | 'course-resume';
  courseId?: number;
  parentFrom?: 'cours' | 'classe-resume';
  parentClassId?: number;
};

@Component({
  selector: 'app-activity-details',
  imports: [],
  templateUrl: './activity-details.html',
  styleUrl: './activity-details.css',
})
export class ActivityDetails {
  private readonly route = inject(ActivatedRoute);
  private readonly router = inject(Router);
  private readonly location = inject(Location);

  private get context(): ActivityDetailsContext {
    const state = history.state as Partial<ActivityDetailsContext>;

    if (state.from === 'course-resume' && typeof state.courseId === 'number') {
      return {
        from: 'course-resume',
        courseId: state.courseId,
        parentFrom: state.parentFrom === 'classe-resume' ? 'classe-resume' : 'cours',
        parentClassId: typeof state.parentClassId === 'number' ? state.parentClassId : undefined,
      };
    }

    if (state.from === 'activites') {
      return { from: 'activites' };
    }

    const sourceFrom = this.route.snapshot.queryParamMap.get('from');
    const rawCourseId = this.route.snapshot.queryParamMap.get('courseId');
    const parsedCourseId = rawCourseId ? Number(rawCourseId) : NaN;
    const parentFrom = this.route.snapshot.queryParamMap.get('parentFrom');
    const rawParentClassId = this.route.snapshot.queryParamMap.get('parentClassId');
    const parsedParentClassId = rawParentClassId ? Number(rawParentClassId) : NaN;

    if (sourceFrom === 'course-resume' && !Number.isNaN(parsedCourseId)) {
      return {
        from: 'course-resume',
        courseId: parsedCourseId,
        parentFrom: parentFrom === 'classe-resume' ? 'classe-resume' : 'cours',
        parentClassId: Number.isNaN(parsedParentClassId) ? undefined : parsedParentClassId,
      };
    }

    return { from: 'activites' };
  }

  private get courseResumeState(): Record<string, string | number> {
    if (this.context.parentFrom === 'classe-resume' && this.context.parentClassId) {
      return {
        from: 'classe-resume',
        classId: this.context.parentClassId,
      };
    }

    return { from: 'cours' };
  }

  goBack(): void {
    if (this.context.from === 'course-resume' && this.context.courseId) {
      this.router.navigate(['/resume-cours', this.context.courseId], {
        state: this.courseResumeState,
      });
      return;
    }

    if (this.context.from === 'activites') {
      this.router.navigate(['/activites']);
      return;
    }

    if (window.history.length > 1) {
      this.location.back();
      return;
    }

    this.router.navigate(['/activites']);
  }
}
