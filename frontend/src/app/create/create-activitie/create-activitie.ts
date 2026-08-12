import { CommonModule } from '@angular/common';
import { Component, inject } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { RouterLink } from '@angular/router';
import { catchError, map, of } from 'rxjs';
import { AcademicUser } from '../../models/academic.model';
import { AdminAcademicDataService } from '../../services/admin-academic-data.service';
import { CourseDataService } from '../../services/course-data.service';
import { UserService } from '../../services/user-service';

@Component({
  selector: 'app-create-activitie',
  imports: [CommonModule, ReactiveFormsModule, RouterLink],
  templateUrl: './create-activitie.html',
  styleUrl: './create-activitie.css',
})
export class CreateActivitie {
  private readonly formBuilder = inject(FormBuilder);
  private readonly courseDataService = inject(CourseDataService);
  private readonly adminDataService = inject(AdminAcademicDataService);
  private readonly userService = inject(UserService);
  private readonly router = inject(Router);

  readonly form = this.formBuilder.group({
    activityType: ['', Validators.required],
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
  saving = false;

  submit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }

    this.saving = true;
    this.courseDataService.createActivity(this.form.getRawValue() as any).subscribe({
      next: () => {
        this.userService.clearUserCache();
        this.adminDataService.clearCache();
        this.courseDataService.clearCache();
        this.router.navigate(['/activites']);
      },
      error: (error) => {
        this.errorMessage = error.error?.error ?? 'Impossible de créer l’activité.';
        this.saving = false;
      },
    });
  }
}
