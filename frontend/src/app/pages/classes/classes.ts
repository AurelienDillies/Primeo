import { Component, inject } from '@angular/core';
import { Classe } from '../../components/classe/classe';
import { UserService } from '../../services/user-service';
import { CommonModule } from '@angular/common';
import { Observable, of } from 'rxjs';
import { catchError, map } from 'rxjs/operators';
import { RouterLink } from '@angular/router';
import { AcademicClass, AcademicUser } from '../../models/academic.model';
import { AcademicStructureService } from '../../services/academic-structure.service';
import { AdminAcademicDataService } from '../../services/admin-academic-data.service';

type ClassesVM = {
  classes: AcademicClass[];
};

@Component({
  selector: 'app-classes',
  standalone: true,
  imports: [CommonModule, Classe, RouterLink],
  templateUrl: './classes.html',
  styleUrl: './classes.css',
})
export class Classes {
  vm$: Observable<ClassesVM>;
  private readonly userService = inject(UserService);
  private readonly academicStructureService = inject(AcademicStructureService);
  private readonly adminAcademicDataService = inject(AdminAcademicDataService);

  constructor() {
    const userId = this.userService.getUserId();

    this.vm$ = this.userService.hasAnyRole(['ROLE_ADMIN'])
      ? this.adminAcademicDataService.getAllClasses().pipe(
          map((classes) => ({ classes })),
          catchError(() => of({ classes: [] }))
        )
      : userId
        ? this.userService.getUserInfo(userId).pipe(
            map((userInfo: AcademicUser) => ({
              classes: this.academicStructureService.getClasses(userInfo),
            })),
            catchError(() => of({ classes: [] }))
          )
        : of({ classes: [] });
  }

  get canCreateClass(): boolean {
    return this.userService.hasAnyRole(['ROLE_ADMIN']);
  }
}