import { Component, Input } from '@angular/core';
import { RouterLink } from "@angular/router";
import { UserService } from '../../services/user-service';
import { AcademicActivity } from '../../models/academic.model';

@Component({
  selector: 'app-activitie',
  imports: [RouterLink],
  templateUrl: './activitie.html',
  styleUrl: './activitie.css',
})
export class Activitie {
  @Input() activity?: AcademicActivity;
  @Input() sourceCourseId?: number;
  @Input() parentFrom?: 'cours' | 'classe-resume';
  @Input() parentClassId?: number;

  readonly rolesForChange = ['ROLE_ADMIN', 'ROLE_TEACHER'];

  constructor(private readonly userService: UserService) { }

  get canEdit(): boolean {
    return this.userService.hasAnyRole(this.rolesForChange);
  }

  get detailsState(): Record<string, string | number> {
    if (this.sourceCourseId) {
      const state: Record<string, string | number> = {
        from: 'course-resume',
        courseId: this.sourceCourseId,
        parentFrom: this.parentFrom ?? 'cours',
      };

      if (this.parentClassId) {
        state['parentClassId'] = this.parentClassId;
      }

      return state;
    }

    return { from: 'activites' };
  }
}