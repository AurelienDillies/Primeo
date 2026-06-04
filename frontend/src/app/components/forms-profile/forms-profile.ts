import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { UserService } from '../../services/user-service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-forms-profile',
  imports: [FormsModule],
  templateUrl: './forms-profile.html',
  styleUrl: './forms-profile.css',
})
export class FormsProfile {
  firstname: string = '';
  lastname: string = '';
  email: string = '';
  password: string = '';

    constructor(private userService: UserService, private router: Router) {}

  onSubmit() {
    const user = {
      first_name: this.firstname,
      last_name: this.lastname,
      email: this.email,
      password: this.password,
    };
    this.userService.update(user).subscribe({
      next: () => {
        console.log('Profile updated successfully');
        this.router.navigate(['/profile']);
      }
    });
  }
}
