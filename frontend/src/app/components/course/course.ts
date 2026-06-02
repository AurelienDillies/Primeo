import { Component } from '@angular/core';
import { RouterLink } from "@angular/router";
import { UserService } from '../../services/user-service';

@Component({
  selector: 'app-course',
  imports: [RouterLink],
  templateUrl: './course.html',
  styleUrl: './course.css',
})
export class Course {
    readonly rolesForChange = ['ROLE_ADMIN', 'ROLE_TEACHER'];
  
    constructor(public userService: UserService) {}
}
