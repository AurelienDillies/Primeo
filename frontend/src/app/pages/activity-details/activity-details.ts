import { Component, inject } from '@angular/core';
import { CommonModule, Location } from '@angular/common';
import { ActivatedRoute, Router } from '@angular/router';
import { Observable, of } from 'rxjs';
import { catchError, map, startWith, switchMap } from 'rxjs/operators';
import { AcademicActivity, AcademicUser } from '../../models/academic.model';
import { AcademicStructureService } from '../../services/academic-structure.service';
import { AdminAcademicDataService } from '../../services/admin-academic-data.service';
import { CourseDataService } from '../../services/course-data.service';
import { UserService } from '../../services/user-service';

type ActivityDetailsContext = {
  from: 'activites' | 'course-resume';
  courseId?: number;
  activityId?: number;
  parentFrom?: 'cours' | 'classe-resume';
  parentClassId?: number;
};

type DetailCard = {
  title: string;
  content: string;
};

type DetailPanel = {
  title: string;
  description: string;
  placeholder: string;
};

type DetailResource = {
  label: string;
  url: string | null;
};

type DetailSidePanel = {
  title: string;
  resources: DetailResource[];
};

type ActivityDetailsVM = {
  loading: boolean;
  error: string | null;
  infoCards: DetailCard[];
  mainPanel: DetailPanel;
  sidePanel: DetailSidePanel;
};

@Component({
  selector: 'app-activity-details',
  imports: [CommonModule],
  templateUrl: './activity-details.html',
  styleUrl: './activity-details.css',
})
export class ActivityDetails {
  readonly pageTitle = "Contenu de l'activite";
  readonly vm$: Observable<ActivityDetailsVM>;

  private readonly route = inject(ActivatedRoute);
  private readonly router = inject(Router);
  private readonly location = inject(Location);
  private readonly userService = inject(UserService);
  private readonly adminAcademicDataService = inject(AdminAcademicDataService);
  private readonly academicStructureService = inject(AcademicStructureService);
  private readonly courseDataService = inject(CourseDataService);

  constructor() {
    this.vm$ = this.route.queryParamMap.pipe(
      map(() => this.context),
      switchMap((context) => {
        if (!context.activityId) {
          return of(this.createVm({
            loading: false,
            error: 'Aucune activite selectionnee.',
            activity: null,
          }));
        }

        return this.getActivityByContext(context).pipe(
          map((activity) => this.createVm({
            loading: false,
            error: activity ? null : 'Activite introuvable.',
            activity,
          })),
          catchError(() => of(this.createVm({
            loading: false,
            error: 'Impossible de charger les informations de l activite.',
            activity: null,
          }))),
          startWith(this.createVm({
            loading: true,
            error: null,
            activity: null,
          }))
        );
      })
    );
  }

  private get context(): ActivityDetailsContext {
    const state = history.state as Partial<ActivityDetailsContext>;

    if (state.from === 'course-resume' && typeof state.courseId === 'number') {
      return {
        from: 'course-resume',
        courseId: state.courseId,
        activityId: typeof state.activityId === 'number' ? state.activityId : undefined,
        parentFrom: state.parentFrom === 'classe-resume' ? 'classe-resume' : 'cours',
        parentClassId: typeof state.parentClassId === 'number' ? state.parentClassId : undefined,
      };
    }

    if (state.from === 'activites') {
      return {
        from: 'activites',
        activityId: typeof state.activityId === 'number' ? state.activityId : undefined,
      };
    }

    const sourceFrom = this.route.snapshot.queryParamMap.get('from');
    const rawCourseId = this.route.snapshot.queryParamMap.get('courseId');
    const parsedCourseId = rawCourseId ? Number(rawCourseId) : NaN;
    const rawActivityId = this.route.snapshot.queryParamMap.get('activityId');
    const parsedActivityId = rawActivityId ? Number(rawActivityId) : NaN;
    const parentFrom = this.route.snapshot.queryParamMap.get('parentFrom');
    const rawParentClassId = this.route.snapshot.queryParamMap.get('parentClassId');
    const parsedParentClassId = rawParentClassId ? Number(rawParentClassId) : NaN;

    if (sourceFrom === 'course-resume' && !Number.isNaN(parsedCourseId)) {
      return {
        from: 'course-resume',
        courseId: parsedCourseId,
        activityId: Number.isNaN(parsedActivityId) ? undefined : parsedActivityId,
        parentFrom: parentFrom === 'classe-resume' ? 'classe-resume' : 'cours',
        parentClassId: Number.isNaN(parsedParentClassId) ? undefined : parsedParentClassId,
      };
    }

    if (!Number.isNaN(parsedActivityId)) {
      return {
        from: 'activites',
        activityId: parsedActivityId,
      };
    }

    return { from: 'activites', activityId: typeof state.activityId === 'number' ? state.activityId : undefined };
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

  get actionLabel(): string {
    return this.context.from === 'course-resume' ? 'Retour au resume du cours' : 'Voir les activites';
  }

  get actionCommands(): (string | number)[] {
    if (this.context.from === 'course-resume' && this.context.courseId) {
      return ['/resume-cours', this.context.courseId];
    }

    return ['/activites'];
  }

  get actionState(): Record<string, string | number> | undefined {
    if (this.context.from === 'course-resume' && this.context.courseId) {
      return this.courseResumeState;
    }

    return undefined;
  }

  private getActivityByContext(context: ActivityDetailsContext): Observable<AcademicActivity | null> {
    if (!context.activityId) {
      return of(null);
    }

      if (context.from === 'course-resume' && context.courseId) {
        return this.courseDataService.getActivitiesByCourseId(context.courseId).pipe(
          map((activities) => activities.find((activity) => activity.id === context.activityId) ?? null)
        );
      }

      if (this.userService.hasAnyRole(['ROLE_ADMIN'])) {
        return this.adminAcademicDataService.getAllActivities().pipe(
          map((activities) => activities.find((activity) => activity.id === context.activityId) ?? null)
        );
      }

      const userId = this.userService.getUserId();
      if (!userId) {
        return of(null);
      }

      return this.userService.getUserInfo(userId).pipe(
        map((userInfo: AcademicUser) => {
          const activities = this.academicStructureService.getActivities(userInfo);
          return activities.find((activity) => activity.id === context.activityId) ?? null;
        })
      );
  }

  private createVm(input: {
    loading: boolean;
    error: string | null;
    activity: AcademicActivity | null;
  }): ActivityDetailsVM {
    return {
      loading: input.loading,
      error: input.error,
      infoCards: this.buildInfoCards(input.activity),
      mainPanel: this.buildMainPanel(input.activity),
      sidePanel: this.buildSidePanel(input.activity),
    };
  }

  private buildInfoCards(activity: AcademicActivity | null): DetailCard[] {
    return [
      {
        title: 'Description',
        content: activity?.activityDescription?.trim() || 'Aucune description disponible pour cette activite.',
      },
      {
        title: 'Documents',
        content: activity?.activityType?.trim()
          ? `Type d activite: ${activity.activityType}`
          : 'Aucun document specifique associe.',
      },
    ];
  }

  private buildMainPanel(activity: AcademicActivity | null): DetailPanel {
    return {
      title: activity?.activityTitle?.trim() || 'Activite',
      description: activity?.activityDescription?.trim() || 'Contenu principal non renseigne.',
      placeholder: activity?.activityType?.trim()
        ? `Type: ${activity.activityType}`
        : 'Aucun contenu principal disponible.',
    };
  }

  private buildSidePanel(activity: AcademicActivity | null): DetailSidePanel {
    const resources = [
      activity?.activityType?.trim() ? `Categorie: ${activity.activityType}` : null,
      activity?.activityDate ? `Date: ${this.formatDate(activity.activityDate)}` : null,
      activity?.id ? `Reference activite: #${activity.id}` : null,
    ].filter((item): item is string => item !== null);

    return {
      title: 'Ressources',
      resources: resources.length
        ? resources.map((item) => ({ label: item, url: null }))
        : [{ label: 'Aucun PDF associe a cette activite.', url: null }],
    };
  }

  isPdfResource(resource: DetailResource): boolean {
    return resource.url !== null && /\.pdf(?:$|[?#])/i.test(resource.url);
  }

  private formatDate(rawDate: string): string {
    const parsedDate = new Date(rawDate);
    if (Number.isNaN(parsedDate.getTime())) {
      return rawDate;
    }

    return parsedDate.toLocaleDateString('fr-FR');
  }

  goToAction(): void {
    this.router.navigate(this.actionCommands, {
      state: this.actionState,
    });
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
