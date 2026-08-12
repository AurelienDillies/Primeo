import { CommonModule } from '@angular/common';
import { Component, inject, OnInit, signal } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { RouterLink } from '@angular/router';
import { catchError, map, of, switchMap, take } from 'rxjs';
import { AdminAcademicDataService } from '../../services/admin-academic-data.service';
import { ClassDataService } from '../../services/class-data.service';
import { CourseDataService } from '../../services/course-data.service';
import { UserService } from '../../services/user-service';

@Component({
  selector: 'app-update-course',
  imports: [CommonModule, ReactiveFormsModule, RouterLink],
  templateUrl: './update-course.html',
  styleUrl: './update-course.css',
})
export class UpdateCourse implements OnInit {
  private readonly formBuilder = inject(FormBuilder);
  private readonly route = inject(ActivatedRoute);
  private readonly router = inject(Router);
  private readonly courseDataService = inject(CourseDataService);
  private readonly classDataService = inject(ClassDataService);
  private readonly adminDataService = inject(AdminAcademicDataService);
  private readonly userService = inject(UserService);
  private courseId: number | null = null;

  readonly form = this.formBuilder.group({
    courseTitle: ['', [Validators.required, Validators.maxLength(100)]],
    courseDescription: ['', Validators.required],
    classId: [null as number | null, Validators.required],
    courseResourcefile: [''],
    courseVideoUrl: [''],
  });
  readonly classes$ = (this.userService.hasAnyRole(['ROLE_ADMIN'])
    ? this.adminDataService.getAllClasses()
    : this.classDataService.getClasses()).pipe(catchError(() => of([])));
  errorMessage = '';
  readonly loading = signal(true);
  saving = false;
  deleting = false;

  ngOnInit(): void {
    this.route.paramMap.pipe(
      take(1),
      map((params) => Number(params.get('courseId'))),
      switchMap((courseId) => {
        this.courseId = Number.isFinite(courseId) && courseId > 0 ? courseId : null;
        if (this.courseId === null) {
          this.errorMessage = 'Cours invalide.';
          this.loading.set(false);
          return of(null);
        }

        return this.courseDataService.getCourseById(this.courseId).pipe(
          catchError(() => {
            this.errorMessage = 'Impossible de charger le cours.';
            return of(null);
          })
        );
      })
    ).subscribe((course) => {
      if (!course) {
        this.loading.set(false);
        return;
      }

      this.form.patchValue({
        courseTitle: course.courseTitle,
        courseDescription: course.courseDescription,
        classId: course.classId ?? null,
        courseResourcefile: course.courseResourcefile ?? '',
        courseVideoUrl: course.courseVideoUrl ?? '',
      });
      this.loading.set(false);
    });
  }

  submit(): void {
    if (this.courseId === null) {
      this.errorMessage = 'Cours invalide.';
      return;
    }

    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }

    this.saving = true;
    this.courseDataService.updateCourse(this.courseId, this.form.getRawValue() as any).subscribe({
      next: () => {
        this.userService.clearUserCache();
        this.adminDataService.clearCache();
        this.router.navigate(['/cours']);
      },
      error: (error) => {
        this.errorMessage = error.error?.error ?? 'Impossible de modifier le cours.';
        this.saving = false;
      },
    });
  }

  deleteCourse(): void {
    if (this.courseId === null || this.deleting || !window.confirm('Voulez-vous vraiment supprimer ce cours et ses données associées ?')) {
      return;
    }

    this.deleting = true;
    this.errorMessage = '';
    this.courseDataService.deleteCourse(this.courseId).subscribe({
      next: () => {
        this.userService.clearUserCache();
        this.adminDataService.clearCache();
        this.courseDataService.clearCache();
        this.router.navigate(['/cours']);
      },
      error: (error) => {
        this.errorMessage = error.error?.error ?? 'Impossible de supprimer le cours.';
        this.deleting = false;
      },
    });
  }
}
