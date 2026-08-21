import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Advance } from '../../components/advance/advance';
import { UserService } from '../../services/user-service';
import { AcademicClass, AcademicProgressCard, AcademicStudent, AcademicUser } from '../../models/academic.model';
import { ProgressDataService } from '../../services/progress-data.service';
import { ParentDataService } from '../../services/parent-data.service';
import { ParentChildSelectionService } from '../../services/parent-child-selection.service';
import { AdminAcademicDataService } from '../../services/admin-academic-data.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-progress',
  imports: [CommonModule, Advance],
  templateUrl: './progress.html',
  styleUrl: './progress.css',
})
export class Progress {
  role: 'student' | 'parent' | 'teacher' | 'admin' = 'student';
  classes: AcademicClass[] = [];
  students: AcademicStudent[] = [];
  children: Array<{ id: number; first_name: string; last_name: string }> = [];
  selectedChildId: number | null = null;
  selectedClassId: number | null = null;
  selectedStudentId: number | null = null;
  progressCards: AcademicProgressCard[] = [];
  loading = true;
  error = '';
  private readonly userService = inject(UserService);
  private readonly progressDataService = inject(ProgressDataService);
  private readonly parentDataService = inject(ParentDataService);
  private readonly parentChildSelectionService = inject(ParentChildSelectionService);
  private readonly adminDataService = inject(AdminAcademicDataService);
  private readonly router = inject(Router);

  goToChildrenPage(): void {
    this.router.navigate(['/enfants']);
  }

  constructor() {
    const roles = this.userService.getUserRoles();
    this.role = roles.includes('ROLE_ADMIN') ? 'admin' : roles.includes('ROLE_TEACHER') ? 'teacher' : roles.includes('ROLE_PARENT') ? 'parent' : 'student';
    this.loadRoleData();
  }

  selectChild(childId: number): void {
    this.selectedChildId = childId;
    this.parentChildSelectionService.setSelectedChildId(childId);
    this.loading = true;
    this.parentDataService.getChildDetail(childId).subscribe({
      next: (child) => {
        this.progressCards = this.progressDataService.getProgressCards(child);
        this.loading = false;
      },
      error: () => this.setError('Impossible de récupérer la progression de cet enfant.'),
    });
  }

  selectClass(classId: number): void {
    this.selectedClassId = classId;
    this.selectedStudentId = null;
    const selectedClass = this.classes.find((academicClass) => academicClass.id === classId);
    this.students = selectedClass?.students ?? [];
    this.progressCards = this.getClassProgress(selectedClass);
    this.loading = false;
  }

  selectStudent(studentId: number): void {
    this.selectedStudentId = studentId;
    const student = this.students.find((candidate) => candidate.id === studentId);
    this.progressCards = this.getClassProgress(this.classes.find((academicClass) => academicClass.id === this.selectedClassId), studentId);
  }

  private loadRoleData(): void {
    const userId = this.userService.getUserId();
    if (this.role === 'parent') {
      this.parentDataService.getMyChildren().subscribe({
        next: (children) => {
          this.children = children;
          const selectedId = this.parentChildSelectionService.getSelectedChildId() ?? children[0]?.id;
          if (selectedId) this.selectChild(selectedId);
          else this.setError('Aucun enfant associé à ce compte parent.');
        },
        error: () => this.setError('Impossible de récupérer les enfants associés à ce parent.'),
      });
      return;
    }
    if (this.role === 'admin') {
      this.adminDataService.getAllClasses().subscribe({
        next: (classes) => {
          this.classes = classes;
          if (classes[0]) this.selectClass(classes[0].id);
          else this.loading = false;
        },
        error: () => this.setError('Impossible de récupérer les classes.'),
      });
      return;
    }
    if (!userId) {
      this.setError('Utilisateur non authentifié.');
      return;
    }
    this.userService.getUserInfo(userId).subscribe({
      next: (userInfo: AcademicUser) => {
        if (this.role === 'student') {
          this.progressCards = this.progressDataService.getProgressCards(userInfo);
        } else {
          this.classes = userInfo.teachingClasses ?? [];
          if (this.classes[0]) this.selectClass(this.classes[0].id);
        }
        this.loading = false;
      },
      error: () => this.setError('Impossible de récupérer les données de suivi.'),
    });
  }

  private getClassProgress(academicClass?: AcademicClass, studentId?: number): AcademicProgressCard[] {
    if (!academicClass) return [];
    return (academicClass.courses ?? []).flatMap((course) => (course.progresses ?? [])
      .filter((progress) => studentId === undefined || progress.student?.id === studentId)
      .map((progress) => ({ ...progress, courseTitle: course.courseTitle, className: academicClass.className })));
  }

  private setError(message: string): void {
    this.error = message;
    this.loading = false;
    this.progressCards = [];
  }
}
