import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { FormsProfile } from '../../components/forms-profile/forms-profile';
import { UserService } from '../../services/user-service';
import { User } from '../../models/user.model';

@Component({
  selector: 'app-profile',
  standalone: true,
  imports: [FormsProfile],
  templateUrl: './profile.html',
  styleUrl: './profile.css',
})
export class Profile implements OnInit {

  user: User | null = null;

  constructor(
    private userService: UserService,
    private cdr: ChangeDetectorRef
  ) { }

  ngOnInit(): void {
    const userId = this.userService.getUserId();

    if (!userId) return;

    this.userService.getUserInfo(userId).subscribe({
      next: (user) => {
        this.user = user;
        this.cdr.detectChanges();
      }
    });
  }
}