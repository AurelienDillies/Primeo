import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsProfile } from '../../components/forms-profile/forms-profile';
import { UserService } from '../../services/user-service';
import { User } from '../../models/user.model';
import { Observable, of } from 'rxjs';
import { catchError } from 'rxjs/operators';

@Component({
  selector: 'app-profile',
  imports: [CommonModule, FormsProfile],
  templateUrl: './profile.html',
  styleUrl: './profile.css',
})
export class Profile {

  user$: Observable<User | null>;

  constructor(private userService: UserService) {

    const userId = this.userService.getUserId();

    this.user$ = userId
      ? this.userService.getUserInfo(userId).pipe(
          catchError(() => of(null))
        )
      : of(null);
  }
}