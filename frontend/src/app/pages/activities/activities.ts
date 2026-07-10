import { CommonModule } from '@angular/common';
import { Component, inject } from '@angular/core';
import { catchError, map } from 'rxjs/operators';
import { Observable, of } from 'rxjs';
import { Activitie } from '../../components/activitie/activitie';
import { RouterLink } from '@angular/router';
import { UserService } from '../../services/user-service';
import { AcademicActivity, AcademicUser } from '../../models/academic.model';
import { AcademicStructureService } from '../../services/academic-structure.service';
import { AdminAcademicDataService } from '../../services/admin-academic-data.service';

type ActivitiesVM = {
  activities: AcademicActivity[];
};

@Component({
  selector: 'app-activities',
  imports: [CommonModule, Activitie, RouterLink],
  templateUrl: './activities.html',
  styleUrl: './activities.css',
})
export class Activities {
  readonly rolesForChange = ['ROLE_ADMIN', 'ROLE_TEACHER'];
  vm$: Observable<ActivitiesVM>;
  private readonly userService = inject(UserService);
  private readonly academicStructureService = inject(AcademicStructureService);
  private readonly adminAcademicDataService = inject(AdminAcademicDataService);

  constructor() {
    const userId = this.userService.getUserId();

    this.vm$ = this.userService.hasAnyRole(['ROLE_ADMIN'])
      ? this.adminAcademicDataService.getAllActivities().pipe(
          map((activities) => ({ activities })),
          catchError(() => of({ activities: [] }))
        )
      : userId
        ? this.userService.getUserInfo(userId).pipe(
            map((userInfo: AcademicUser) => ({
              activities: this.academicStructureService.getActivities(userInfo),
            })),
            catchError(() => of({ activities: [] }))
          )
        : of({ activities: [] });
  }

    get canEdit(): boolean {
      return this.userService.hasAnyRole(this.rolesForChange);
    }
}