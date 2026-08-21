import { CommonModule } from '@angular/common';
import { Component, inject, OnInit, signal } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { RouterLink } from '@angular/router';
import { catchError, map, of, switchMap, take } from 'rxjs';
import { AcademicUser } from '../../models/academic.model';
import { AdminAcademicDataService } from '../../services/admin-academic-data.service';
import { CourseDataService } from '../../services/course-data.service';
import { UserService } from '../../services/user-service';

@Component({
  selector: 'app-update-activitie',
  imports: [CommonModule, ReactiveFormsModule, RouterLink],
  templateUrl: './update-activitie.html',
  styleUrl: './update-activitie.css',
})
export class UpdateActivitie implements OnInit {
  private readonly formBuilder = inject(FormBuilder);
  private readonly route = inject(ActivatedRoute);
  private readonly router = inject(Router);
  private readonly courseDataService = inject(CourseDataService);
  private readonly adminDataService = inject(AdminAcademicDataService);
  private readonly userService = inject(UserService);
  private activityId: number | null = null;

  readonly form = this.formBuilder.group({
    activityType: ['', [Validators.required, Validators.maxLength(100)]],
    activityTitle: ['', [Validators.required, Validators.maxLength(100)]],
    activityDescription: [''],
    activityDate: ['', Validators.required],
    courseId: [null as number | null, Validators.required],
  });
  readonly courses$ = (this.userService.hasAnyRole(['ROLE_ADMIN'])
    ? this.adminDataService.getAllCourses()
    : this.userService.getUserInfo(this.userService.getUserId() ?? undefined).pipe(
      map((user) => {
        const academicUser = user as unknown as AcademicUser;
        return (academicUser.classes ?? academicUser.teachingClasses ?? []).flatMap((classe) => classe.courses ?? []);
      })
    )).pipe(catchError(() => of([])));
  errorMessage = '';
  readonly loading = signal(true);
  saving = false;
  deleting = false;

  ngOnInit(): void {
    this.route.paramMap.pipe(
      take(1),
      map((params) => Number(params.get('activityId'))),
      switchMap((activityId) => {
        this.activityId = Number.isFinite(activityId) && activityId > 0 ? activityId : null;
        if (this.activityId === null) {
          this.errorMessage = 'Activité invalide.';
          this.loading.set(false);
          return of(null);
        }

        return this.courseDataService.getActivityById(this.activityId).pipe(
          catchError(() => {
            this.errorMessage = 'Impossible de charger l’activité.';
            return of(null);
          })
        );
      })
    ).subscribe((activity) => {
      if (!activity) {
        this.loading.set(false);
        return;
      }

      this.form.patchValue({
        activityType: activity.activityType,
        activityTitle: activity.activityTitle,
        activityDescription: activity.activityDescription ?? '',
        activityDate: activity.activityDate.split('T')[0],
        courseId: activity.courseId ?? null,
      });
      this.loading.set(false);
    });
  }

  submit(): void {
    if (this.activityId === null) {
      this.errorMessage = 'Activité invalide.';
      return;
    }

    if (this.form.invalid) {
      this.form.markAllAsTouched();
      this.errorMessage = 'Veuillez corriger les champs signalés avant d’enregistrer l’activité.';
      return;
    }

    this.saving = true;
    this.courseDataService.updateActivity(this.activityId, this.form.getRawValue() as any).subscribe({
      next: () => {
        this.userService.clearUserCache();
        this.adminDataService.clearCache();
        this.courseDataService.clearCache();
        this.router.navigate(['/activites']);
      },
      error: (error) => {
        this.errorMessage = error.error?.error ?? 'Impossible de modifier l’activité.';
        this.saving = false;
      },
    });
  }

  deleteActivity(): void {
    if (this.activityId === null || this.deleting || !window.confirm('Voulez-vous vraiment supprimer cette activité ?')) {
      return;
    }

    this.deleting = true;
    this.errorMessage = '';
    this.courseDataService.deleteActivity(this.activityId).subscribe({
      next: () => {
        this.userService.clearUserCache();
        this.adminDataService.clearCache();
        this.courseDataService.clearCache();
        this.router.navigate(['/activites']);
      },
      error: (error) => {
        this.errorMessage = error.error?.error ?? 'Impossible de supprimer l’activité.';
        this.deleting = false;
      },
    });
  }
}
