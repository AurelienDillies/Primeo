import { CommonModule } from '@angular/common';
import { Component, computed, inject, signal } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { ClassDataService } from '../../services/class-data.service';
import { AcademicStudent, AcademicTeacher } from '../../models/academic.model';
import { catchError, of } from 'rxjs';
import { AdminAcademicDataService } from '../../services/admin-academic-data.service';
import { UserService } from '../../services/user-service';

@Component({
  selector: 'app-create-classe',
  imports: [CommonModule, ReactiveFormsModule, RouterLink],
  templateUrl: './create-classe.html',
  styleUrl: './create-classe.css',
})
export class CreateClasse {
  private readonly formBuilder = inject(FormBuilder);
  private readonly classDataService = inject(ClassDataService);
  private readonly adminDataService = inject(AdminAcademicDataService);
  private readonly userService = inject(UserService);
  private readonly router = inject(Router);

  readonly form = this.formBuilder.group({
    className: ['', [Validators.required, Validators.maxLength(255)]],
    classDescription: [''],
    studentIds: this.formBuilder.control<number[]>([]),
    teacherId: this.formBuilder.control<number | null>(null),
  });
  readonly students = signal<AcademicStudent[]>([]);
  readonly selectedStudentIds = signal<number[]>([]);
  readonly teachers = signal<AcademicTeacher[]>([]);
  readonly studentSearch = signal('');
  readonly selectedStudents = computed(() => {
    const selectedIds = this.selectedStudentIds();
    return this.students().filter((student) => selectedIds.includes(student.id));
  });
  readonly availableStudents = computed(() => {
    const selectedIds = this.selectedStudentIds();
    const search = this.studentSearch().trim().toLocaleLowerCase();
    return this.students().filter((student) => {
      const matchesSearch = !search || `${student.first_name} ${student.last_name} ${student.email}`.toLocaleLowerCase().includes(search);
      return !selectedIds.includes(student.id) && matchesSearch;
    });
  });
  errorMessage = '';
  saving = false;

  constructor() {
    this.classDataService.getStudents().pipe(catchError(() => of([] as AcademicStudent[]))).subscribe((students) => this.students.set(students));
    this.classDataService.getTeachers().pipe(catchError(() => of([] as AcademicTeacher[]))).subscribe((teachers) => this.teachers.set(teachers));
  }

  submit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      this.errorMessage = 'Veuillez corriger les champs signalés avant de créer la classe.';
      return;
    }

    this.saving = true;
    this.errorMessage = '';
    this.classDataService.createClass({
      className: this.form.controls.className.value?.trim() ?? '',
      classDescription: this.form.controls.classDescription.value?.trim() || null,
      studentIds: this.selectedStudentIds(),
      teacherId: this.form.controls.teacherId.value ?? null,
    }).subscribe({
      next: () => {
        this.classDataService.clearCache();
        this.adminDataService.clearCache();
        this.userService.clearUserCache();
        this.router.navigate(['/classes']);
      },
      error: (error) => {
        this.errorMessage = error.error?.error ?? 'Impossible de créer la classe.';
        this.saving = false;
      },
    });
  }

  addStudent(studentId: number): void {
    const selected = this.selectedStudentIds();
    if (selected.includes(studentId)) {
      return;
    }
    const updated = [...selected, studentId];
    this.selectedStudentIds.set(updated);
    this.form.controls.studentIds.setValue(updated);
  }

  removeStudent(studentId: number): void {
    const updated = this.selectedStudentIds().filter((id) => id !== studentId);
    this.selectedStudentIds.set(updated);
    this.form.controls.studentIds.setValue(updated);
  }
}
