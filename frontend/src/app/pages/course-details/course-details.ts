import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, Router } from '@angular/router';
import { Location } from '@angular/common';
import { Observable, of } from 'rxjs';
import { catchError, map, startWith, switchMap } from 'rxjs/operators';
import { AcademicCourse, AcademicUser } from '../../models/academic.model';
import { AcademicStructureService } from '../../services/academic-structure.service';
import { AdminAcademicDataService } from '../../services/admin-academic-data.service';
import { UserService } from '../../services/user-service';

type CourseNavigationContext = {
  from: 'cours' | 'classe-resume';
  classId?: number;
};

type DetailCard = {
  title: string;
  content: string;
};

type DetailPanel = {
  title: string;
  description: string;
  placeholder: string;
  videoUrl: string | null;
};

type DetailResource = {
  label: string;
  url: string | null;
};

type DetailSidePanel = {
  title: string;
  resources: DetailResource[];
};

type CourseDetailsVM = {
  loading: boolean;
  error: string | null;
  courseId: number | null;
  infoCards: DetailCard[];
  mainPanel: DetailPanel;
  sidePanel: DetailSidePanel;
};

@Component({
  selector: 'app-course-details',
  imports: [CommonModule],
  templateUrl: './course-details.html',
  styleUrl: './course-details.css',
})
export class CourseDetails {
  readonly pageTitle = 'Contenu du cours';
  readonly actionLabel = 'Voir les activites du cours';
  readonly vm$: Observable<CourseDetailsVM>;
  private readonly apiOrigin = 'http://localhost:8080';

  private readonly route = inject(ActivatedRoute);
  private readonly router = inject(Router);
  private readonly location = inject(Location);
  private readonly userService = inject(UserService);
  private readonly adminAcademicDataService = inject(AdminAcademicDataService);
  private readonly academicStructureService = inject(AcademicStructureService);

  constructor() {
    this.vm$ = this.route.paramMap.pipe(
      map((params) => {
        const rawCourseId = params.get('courseId');
        const parsedCourseId = rawCourseId ? Number(rawCourseId) : NaN;
        return Number.isNaN(parsedCourseId) ? null : parsedCourseId;
      }),
      switchMap((courseId) => {
        if (!courseId) {
          return of(this.createVm({
            loading: false,
            error: 'Aucun cours selectionne.',
            courseId: null,
            course: null,
          }));
        }

        return this.getCourseById(courseId).pipe(
          map((course) => this.createVm({
            loading: false,
            error: course ? null : 'Cours introuvable.',
            courseId,
            course,
          })),
          catchError(() => of(this.createVm({
            loading: false,
            error: 'Impossible de charger les informations du cours.',
            courseId,
            course: null,
          }))),
          startWith(this.createVm({
            loading: true,
            error: null,
            courseId,
            course: null,
          }))
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

  get resumeState(): CourseNavigationContext {
    return this.context;
  }

  get fallbackActionCommands(): (string | number)[] {
    return ['/resume-cours'];
  }

  buildActionCommands(courseId: number): (string | number)[] {
    return ['/resume-cours', courseId];
  }

  private getCourseById(courseId: number): Observable<AcademicCourse | null> {
    if (this.userService.hasAnyRole(['ROLE_ADMIN'])) {
      return this.adminAcademicDataService.getAllCourses().pipe(
        map((courses) => courses.find((course) => course.id === courseId) ?? null)
      );
    }

    const userId = this.userService.getUserId();
    if (!userId) {
      return of(null);
    }

    return this.userService.getUserInfo(userId).pipe(
      map((userInfo: AcademicUser) => {
        const courses = this.academicStructureService.getCourses(userInfo);
        return courses.find((course) => course.id === courseId) ?? null;
      })
    );
  }

  private createVm(input: {
    loading: boolean;
    error: string | null;
    courseId: number | null;
    course: AcademicCourse | null;
  }): CourseDetailsVM {
    return {
      loading: input.loading,
      error: input.error,
      courseId: input.courseId,
      infoCards: this.buildInfoCards(input.course),
      mainPanel: this.buildMainPanel(input.course),
      sidePanel: this.buildSidePanel(input.course),
    };
  }

  private buildInfoCards(course: AcademicCourse | null): DetailCard[] {
    return [
      {
        title: 'Description',
        content: course?.courseDescription?.trim() || 'Aucune description disponible pour ce cours.',
      },
      {
        title: 'Documents',
        content: course?.courseResourcefile?.trim()
          ? `Ressource principale: ${course.courseResourcefile}`
          : 'Aucun document associe pour le moment.',
      },
    ];
  }

  private buildMainPanel(course: AcademicCourse | null): DetailPanel {
    return {
      title: course?.courseTitle?.trim() || 'Cours',
      description: course?.courseDescription?.trim() || 'Contenu principal non renseigne.',
      placeholder: course?.courseVideoUrl?.trim()
        ? `Video: ${course.courseVideoUrl}`
        : 'Aucune video associee pour ce cours.',
      videoUrl: this.toUsableMediaUrl(course?.courseVideoUrl),
    };
  }

  private buildSidePanel(course: AcademicCourse | null): DetailSidePanel {
    const resources = this.extractResources(course?.courseResourcefile ?? null);

    return {
      title: 'Ressources',
      resources: resources.length
        ? resources
        : [{ label: 'Aucune ressource disponible.', url: null }],
    };
  }

  private extractResources(resourceFile: string | null): DetailResource[] {
    if (!resourceFile) {
      return [];
    }

    return resourceFile
      .split(/[;,]/)
      .map((item) => item.trim())
      .filter((item) => item.length > 0)
      .map((item) => ({
        label: item,
        url: this.toUsableMediaUrl(item),
      }));
  }

  private toUsableMediaUrl(value: string | null | undefined): string | null {
    const trimmedValue = value?.trim();
    if (!trimmedValue) {
      return null;
    }

    if (/^https?:\/\//i.test(trimmedValue)) {
      return trimmedValue;
    }

    return new URL(trimmedValue, `${this.apiOrigin}/`).toString();
  }

  isPdfResource(resource: DetailResource): boolean {
    return resource.url !== null && /\.pdf(?:$|[?#])/i.test(resource.url);
  }

  goToAction(): void {
    const rawCourseId = this.route.snapshot.paramMap.get('courseId');
    const parsedCourseId = rawCourseId ? Number(rawCourseId) : NaN;

    if (!Number.isNaN(parsedCourseId)) {
      this.router.navigate(this.buildActionCommands(parsedCourseId), {
        state: this.resumeState,
      });
      return;
    }

    this.router.navigate(this.fallbackActionCommands, {
      state: this.resumeState,
    });
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
