import { Component } from '@angular/core';
import { RouterLink } from "@angular/router";
import { UserService } from '../../services/user-service';

@Component({
  selector: 'app-activitie',
  imports: [RouterLink],
  templateUrl: './activitie.html',
  styleUrl: './activitie.css',
})
export class Activitie {
      readonly rolesForChange = ['ROLE_ADMIN', 'ROLE_TEACHER'];
    
      constructor(public userService: UserService) {}
}
