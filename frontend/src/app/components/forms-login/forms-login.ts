import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { UserService } from '../../services/user-service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-forms-login',
  imports: [FormsModule],
  templateUrl: './forms-login.html',
  styleUrl: './forms-login.css',
})
export class FormsLogin {
  email: string = '';
  password: string = '';

  constructor(private userService: UserService, private router: Router) {}

  onSubmit() {
    console.log(this.email, this.password);
    this.userService.login(this.email, this.password).subscribe({
      
      next: (response: any) => {
        const token = response.token;
        this.userService.setToken(token);

        const roles = this.userService.getUserRoles();

        if (roles.includes('ROLE_ADMIN')) {
          this.router.navigate(['/classes']);
          return;
        }

        if (roles.includes('ROLE_TEACHER') || roles.includes('ROLE_STUDENT')) {
          this.router.navigate(['/classes']);
          return;
        }

        if (roles.includes('ROLE_PARENT')) {
          this.router.navigate(['/enfants']);
          return;
        }

        this.router.navigate(['/profil']);
      },
      error: err => console.error('Login failed', err)
    });
  }
}
