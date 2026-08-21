import { CommonModule } from '@angular/common';
import { Component, inject } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { RouterLink } from '@angular/router';
import { catchError, of } from 'rxjs';
import { AdminAcademicDataService } from '../../services/admin-academic-data.service';
import { ClassDataService } from '../../services/class-data.service';
import { CourseDataService } from '../../services/course-data.service';
import { UserService } from '../../services/user-service';

@Component({
  selector: 'app-create-course',
  imports: [CommonModule, ReactiveFormsModule, RouterLink],
  templateUrl: './create-course.html',
  styleUrl: './create-course.css',
})
export class CreateCourse {
  private readonly formBuilder = inject(FormBuilder);
  private readonly courseDataService = inject(CourseDataService);
  private readonly classDataService = inject(ClassDataService);
  private readonly adminDataService = inject(AdminAcademicDataService);
  private readonly userService = inject(UserService);
  private readonly router = inject(Router);

  readonly form = this.formBuilder.group({
    courseTitle: ['', [Validators.required, Validators.maxLength(100)]],
    courseDescription: ['', Validators.required],
    classId: [null as number | null, Validators.required],
    courseResourcefile: ['', Validators.maxLength(255)],
    courseVideoUrl: ['', Validators.maxLength(255)],
  });
  readonly classes$ = (this.userService.hasAnyRole(['ROLE_ADMIN'])
    ? this.adminDataService.getAllClasses()
    : this.classDataService.getClasses()).pipe(catchError(() => of([])));
  errorMessage = '';
  saving = false;

  submit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      this.errorMessage = 'Veuillez corriger les champs signalés avant de créer le cours.';
      return;
    }

    this.saving = true;
    this.errorMessage = '';
    this.courseDataService.createCourse(this.form.getRawValue() as any).subscribe({
      next: () => {
        this.userService.clearUserCache();
        this.adminDataService.clearCache();
        this.router.navigate(['/cours']);
      },
      error: (error) => {
        this.errorMessage = error.error?.error ?? 'Impossible de créer le cours.';
        this.saving = false;
      },
    });
  }
}
