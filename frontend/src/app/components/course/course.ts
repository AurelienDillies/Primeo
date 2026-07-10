import { Component, Input } from '@angular/core';
import { RouterLink } from "@angular/router";
import { UserService } from '../../services/user-service';
import { AcademicCourse } from '../../models/academic.model';

@Component({
  selector: 'app-course',
  imports: [RouterLink],
  templateUrl: './course.html',
  styleUrl: './course.css',
})
export class Course {
  @Input() course?: AcademicCourse;
  @Input() sourceClassId?: number;

  readonly rolesForChange = ['ROLE_ADMIN', 'ROLE_TEACHER'];

  constructor(private readonly userService: UserService) {}

  get canEdit(): boolean {
    return this.userService.hasAnyRole(this.rolesForChange);
  }

  get detailsRoute(): (string | number)[] {
    return this.course?.id
      ? ['/cours-details', this.course.id]
      : ['/cours-details'];
  }

  get detailsState(): Record<string, string | number> {
    if (this.sourceClassId) {
      return {
        from: 'classe-resume',
        classId: this.sourceClassId,
      };
    }

    return { from: 'cours' };
  }
}
