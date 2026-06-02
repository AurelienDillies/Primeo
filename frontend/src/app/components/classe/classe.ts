import { Component } from '@angular/core';
import { RouterLink } from "@angular/router";
import { UserService } from '../../services/user-service';

@Component({
  selector: 'app-classe',
  imports: [RouterLink],
  templateUrl: './classe.html',
  styleUrl: './classe.css',
})
export class Classe {

  readonly rolesForChange = ['ROLE_ADMIN', 'ROLE_TEACHER'];

  constructor(public userService: UserService) {}

}
