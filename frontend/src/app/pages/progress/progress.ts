import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Advance } from '../../components/advance/advance';
import { UserService } from '../../services/user-service';
import { AcademicActivity, AcademicCourse, AcademicUser } from '../../models/academic.model';
import { AcademicProgressCard } from '../../models/academic.model';
import { ProgressDataService } from '../../services/progress-data.service';
import { Observable, of } from 'rxjs';
import { catchError, map, switchMap } from 'rxjs/operators';
import { ParentDataService } from '../../services/parent-data.service';
import { ParentChildSelectionService } from '../../services/parent-child-selection.service';
import { Router } from '@angular/router';
import { AcademicStructureService } from '../../services/academic-structure.service';

type ProgressVM = {
  isParent: boolean;
  selectedChildName?: string;
  needsChildSelection?: boolean;
  courses: AcademicCourse[];
  activities: AcademicActivity[];
  progressCards: AcademicProgressCard[];
  error?: string;
};

@Component({
  selector: 'app-progress',
  imports: [CommonModule, Advance],
  templateUrl: './progress.html',
  styleUrl: './progress.css',
})
export class Progress {
  vm$: Observable<ProgressVM>;
  private readonly userService = inject(UserService);
  private readonly progressDataService = inject(ProgressDataService);
  private readonly parentDataService = inject(ParentDataService);
  private readonly parentChildSelectionService = inject(ParentChildSelectionService);
  private readonly academicStructureService = inject(AcademicStructureService);
  private readonly router = inject(Router);

  goToChildrenPage(): void {
    this.router.navigate(['/enfants']);
  }

  private buildParentVm(childInfo: AcademicUser): ProgressVM {
    const courses = this.academicStructureService.getCourses(childInfo);

    return {
      isParent: true,
      selectedChildName: `${childInfo.first_name ?? ''} ${childInfo.last_name ?? ''}`.trim(),
      progressCards: this.progressDataService.getProgressCards(childInfo),
      courses,
      activities: this.academicStructureService.getActivities(childInfo),
    };
  }

  private getAutoSelectedChildVm(): Observable<ProgressVM> {
    return this.parentDataService.getMyChildren().pipe(
      switchMap((children) => {
        const firstChild = children[0];
        if (!firstChild) {
          return of({
            isParent: true,
            needsChildSelection: true,
            progressCards: [],
            courses: [],
            activities: [],
          });
        }

        this.parentChildSelectionService.setSelectedChildId(firstChild.id);

        return this.parentDataService.getChildDetail(firstChild.id).pipe(
          map((childInfo: AcademicUser) => this.buildParentVm(childInfo)),
          catchError(() =>
            of({
              isParent: true,
              progressCards: [],
              courses: [],
              activities: [],
              error: 'Impossible de récupérer les données de suivi pour cet enfant.',
            })
          )
        );
      }),
      catchError(() =>
        of({
          isParent: true,
          progressCards: [],
          courses: [],
          activities: [],
          error: 'Impossible de récupérer les enfants associés à ce parent.',
        })
      )
    );
  }

  constructor() {
    const userRoles = this.userService.getUserRoles();
    const isParent = userRoles.includes('ROLE_PARENT');

    if (isParent) {
      const selectedChildId = this.parentChildSelectionService.getSelectedChildId();

      if (!selectedChildId) {
        this.vm$ = this.getAutoSelectedChildVm();

        return;
      }

      this.vm$ = this.parentDataService.getChildDetail(selectedChildId).pipe(
        map((childInfo: AcademicUser) => this.buildParentVm(childInfo)),
        catchError(() => this.getAutoSelectedChildVm())
      );

      return;
    }

    const userId = this.userService.getUserId();

    this.vm$ = userId
      ? this.userService.getUserInfo(userId).pipe(
          map((userInfo: AcademicUser) => ({
            isParent: false,
            progressCards: this.progressDataService.getProgressCards(userInfo),
            courses: this.academicStructureService.getCourses(userInfo),
            activities: this.academicStructureService.getActivities(userInfo),
          })),
          catchError(() => of({ isParent: false, progressCards: [], courses: [], activities: [] }))
        )
      : of({ isParent: false, progressCards: [], courses: [], activities: [] });
  }
}
