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

  readonly rolesForChange = ['ROLE_ADMIN', 'ROLE_TEACHER'];

  constructor(private readonly userService: UserService) { }

  get canEdit(): boolean {
    return this.userService.hasAnyRole(this.rolesForChange);
  }
}