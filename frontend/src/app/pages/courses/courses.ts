import { CommonModule } from '@angular/common';
import { Component, inject } from '@angular/core';
import { catchError, map } from 'rxjs/operators';
import { Observable, of } from 'rxjs';
import { Course } from '../../components/course/course';
import { UserService } from '../../services/user-service';
import { RouterLink } from '@angular/router';
import { AcademicCourse, AcademicUser } from '../../models/academic.model';
import { AcademicStructureService } from '../../services/academic-structure.service';
import { AdminAcademicDataService } from '../../services/admin-academic-data.service';

type CoursesVM = {
  courses: AcademicCourse[];
};

@Component({
  selector: 'app-courses',
  imports: [CommonModule, Course, RouterLink],
  templateUrl: './courses.html',
  styleUrl: './courses.css',
})
export class Courses {
  readonly rolesForChange = ['ROLE_ADMIN', 'ROLE_TEACHER'];
  vm$: Observable<CoursesVM>;
  private readonly userService = inject(UserService);
  private readonly academicStructureService = inject(AcademicStructureService);
  private readonly adminAcademicDataService = inject(AdminAcademicDataService);

  constructor() {
    const userId = this.userService.getUserId();

    this.vm$ = this.userService.hasAnyRole(['ROLE_ADMIN'])
      ? this.adminAcademicDataService.getAllCourses().pipe(
          map((courses) => ({ courses })),
          catchError(() => of({ courses: [] }))
        )
      : userId
        ? this.userService.getUserInfo(userId).pipe(
            map((userInfo: AcademicUser) => ({
              courses: this.academicStructureService.getCourses(userInfo),
            })),
            catchError(() => of({ courses: [] }))
          )
        : of({ courses: [] });
  }

    get canEdit(): boolean {
      return this.userService.hasAnyRole(this.rolesForChange);
    }
}
