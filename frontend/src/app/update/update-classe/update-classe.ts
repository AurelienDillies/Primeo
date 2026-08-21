import { CommonModule } from '@angular/common';
import { Component, computed, inject, OnInit, signal } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { catchError, map, of, switchMap, take } from 'rxjs';
import { ClassDataService } from '../../services/class-data.service';
import { AcademicStudent, AcademicTeacher } from '../../models/academic.model';
import { AdminAcademicDataService } from '../../services/admin-academic-data.service';
import { UserService } from '../../services/user-service';

@Component({
  selector: 'app-update-classe',
  imports: [CommonModule, ReactiveFormsModule, RouterLink],
  templateUrl: './update-classe.html',
  styleUrl: './update-classe.css',
})
export class UpdateClasse implements OnInit {
  private readonly formBuilder = inject(FormBuilder);
  private readonly route = inject(ActivatedRoute);
  private readonly router = inject(Router);
  private readonly classDataService = inject(ClassDataService);
  private readonly adminDataService = inject(AdminAcademicDataService);
  private readonly userService = inject(UserService);
  private classId: number | null = null;

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
  readonly loading = signal(true);
  readonly currentTeacherName = signal<string | null>(null);
  errorMessage = '';
  saving = false;
  deleting = false;

  get canDelete(): boolean {
    return this.userService.hasAnyRole(['ROLE_ADMIN']);
  }

  get canChangeTeacher(): boolean {
    return this.userService.hasAnyRole(['ROLE_ADMIN']);
  }

  constructor() {
    this.classDataService.getStudents().pipe(catchError(() => of([] as AcademicStudent[]))).subscribe((students) => this.students.set(students));
    if (this.canChangeTeacher) {
      this.classDataService.getTeachers().pipe(catchError(() => of([] as AcademicTeacher[]))).subscribe((teachers) => this.teachers.set(teachers));
    }
  }

  ngOnInit(): void {
    this.route.paramMap.pipe(
      take(1),
      map((params) => Number(params.get('classId'))),
      switchMap((classId) => {
        this.classId = Number.isFinite(classId) && classId > 0 ? classId : null;
        if (this.classId === null) {
          this.errorMessage = 'Classe invalide.';
          this.loading.set(false);
          return of(null);
        }

        return this.classDataService.getClassById(this.classId).pipe(
          catchError(() => {
            this.errorMessage = 'Impossible de charger la classe.';
            return of(null);
          })
        );
      })
    ).subscribe((classe) => {
      if (!classe) {
        this.loading.set(false);
        return;
      }

      this.form.patchValue({
        className: classe.className,
        classDescription: classe.classDescription ?? '',
        studentIds: classe.studentIds ?? [],
        teacherId: classe.teacherId ?? null,
      });
      this.currentTeacherName.set(classe.teacherName ?? null);
      this.selectedStudentIds.set(classe.studentIds ?? []);
      this.loading.set(false);
    });
  }

  submit(): void {
    if (this.classId === null) {
      this.errorMessage = 'Classe invalide.';
      return;
    }
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      this.errorMessage = 'Veuillez corriger les champs signalés avant d’enregistrer la classe.';
      return;
    }

    this.saving = true;
    const payload: {
      className: string;
      classDescription: string | null;
      studentIds: number[];
      teacherId?: number | null;
    } = {
      className: this.form.controls.className.value?.trim() ?? '',
      classDescription: this.form.controls.classDescription.value?.trim() || null,
      studentIds: this.selectedStudentIds(),
    };
    if (this.canChangeTeacher) {
      payload.teacherId = this.form.controls.teacherId.value ?? null;
    }

    this.classDataService.updateClass(this.classId, payload).subscribe({
      next: () => {
        this.userService.clearUserCache();
        this.adminDataService.clearCache();
        this.classDataService.clearCache();
        this.router.navigate(['/classes']);
      },
      error: (error) => {
        this.errorMessage = error.error?.error ?? 'Impossible de modifier la classe.';
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

  deleteClass(): void {
    if (this.classId === null || this.deleting || !window.confirm('Voulez-vous vraiment supprimer cette classe et ses données associées ?')) {
      return;
    }

    this.deleting = true;
    this.errorMessage = '';
    this.classDataService.deleteClass(this.classId).subscribe({
      next: () => {
        this.userService.clearUserCache();
        this.adminDataService.clearCache();
        this.classDataService.clearCache();
        this.router.navigate(['/classes']);
      },
      error: (error) => {
        this.errorMessage = error.error?.error ?? 'Impossible de supprimer la classe.';
        this.deleting = false;
      },
    });
  }
}
