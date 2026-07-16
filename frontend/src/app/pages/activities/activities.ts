import { Component } from '@angular/core';
import { Activitie } from '../../components/activitie/activitie';
import { RouterLink } from '@angular/router';
import { UserService } from '../../services/user-service';

@Component({
  selector: 'app-activities',
  imports: [Activitie, RouterLink],
  templateUrl: './activities.html',
  styleUrl: './activities.css',
})
export class Activities {
  readonly rolesForChange = ['ROLE_ADMIN', 'ROLE_TEACHER'];

  constructor(public userService: UserService) { }
}