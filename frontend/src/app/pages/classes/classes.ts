import { Component } from '@angular/core';
import { Classe } from '../../components/classe/classe';
import { Student } from '../../models/student.model';
import { User } from '../../models/user.model';
import { UserService } from '../../services/user-service';
import { CommonModule } from '@angular/common';
import { Observable, of } from 'rxjs';
import { map } from 'rxjs/operators';

type ClassesVM = {
  classes: any[];
  student: Student | null;
  teacher: User | null;
};

@Component({
  selector: 'app-classes',
  standalone: true,
  imports: [CommonModule, Classe],
  templateUrl: './classes.html',
  styleUrl: './classes.css',
})
export class Classes {

  vm$: Observable<ClassesVM>;

  constructor(private userService: UserService) {

    const userId = this.userService.getUserId();

    this.vm$ = userId
      ? this.userService.getUserInfo(userId).pipe(
          map((userInfo: any) => ({
            classes: userInfo.classes ?? [],
            student: userInfo.roles.includes('ROLE_STUDENT')
              ? userInfo as Student
              : null,
            teacher: userInfo.roles.includes('ROLE_TEACHER')
              ? userInfo as User
              : null
          }))
        )
      : of({
          classes: [],
          student: null,
          teacher: null
        });
  }
}