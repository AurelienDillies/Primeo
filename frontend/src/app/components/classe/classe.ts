import { Component, Input } from '@angular/core';
import { RouterLink } from "@angular/router";
import { UserService } from '../../services/user-service';
import { AcademicClass } from '../../models/academic.model';

@Component({
  selector: 'app-classe',
  imports: [RouterLink],
  templateUrl: './classe.html',
  styleUrl: './classe.css',
})
export class Classe {
  @Input() classe?: AcademicClass;

  readonly rolesForChange = ['ROLE_ADMIN', 'ROLE_TEACHER'];

  constructor(private readonly userService: UserService) {}

  get canEdit(): boolean {
    return this.userService.hasAnyRole(this.rolesForChange);
  }

}
