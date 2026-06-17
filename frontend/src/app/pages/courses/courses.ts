import { Component } from '@angular/core';
import { Course } from '../../components/course/course';
import { UserService } from '../../services/user-service';
import { RouterLink } from '@angular/router';

@Component({
  selector: 'app-courses',
  imports: [Course, RouterLink],
  templateUrl: './courses.html',
  styleUrl: './courses.css',
})
export class Courses {
  readonly rolesForChange = ['ROLE_ADMIN', 'ROLE_TEACHER'];

  constructor(public userService: UserService) { }
}
