import { Component, Input, OnChanges, SimpleChanges } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { UserService } from '../../services/user-service';
import { Router } from '@angular/router';
import { User } from '../../models/user.model';

@Component({
  selector: 'app-forms-profile',
  standalone: true,
  imports: [FormsModule],
  templateUrl: './forms-profile.html',
  styleUrl: './forms-profile.css',
})
export class FormsProfile implements OnChanges {

  @Input() user: User | null = null;

  first_name = '';
  last_name = '';
  email = '';
  password = '';

  constructor(
    private userService: UserService,
    private router: Router
  ) {}

    ngOnChanges(changes: SimpleChanges): void {
      if (this.user) {
        this.first_name = this.user.first_name;
        this.last_name = this.user.last_name;
        this.email = this.user.email;
      }
    }

  onSubmit(): void {
    const payload: any = {
      first_name: this.first_name,
      last_name: this.last_name,
      email: this.email,
    };

    if (this.password?.trim()) {
      payload.password = this.password;
    }

    this.userService.update(payload).subscribe({
      next: () => {
        console.log('Profil mis à jour');
        this.router.navigate(['/profile']);
      },
      error: (err) => console.error(err)
    });
  }
}