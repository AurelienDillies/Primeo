import { Component } from '@angular/core';
import { FormsModule, NgForm } from '@angular/forms';
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
  errorMessage = '';

  constructor(private userService: UserService, private router: Router) {}

  onSubmit(form: NgForm) {
    if (form.invalid) {
      this.errorMessage = 'Veuillez renseigner une adresse email et un mot de passe.';
      form.control.markAllAsTouched();
      return;
    }

    this.errorMessage = '';
    this.userService.login(this.email, this.password).subscribe({
      
      next: (response: { token: string }) => {
        const token = response.token;
        this.userService.setToken(token);

        const roles = this.userService.getUserRoles();

        if (roles.includes('ROLE_ADMIN')) {
          this.router.navigate(['/utilisateurs']);
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
      error: err => {
        this.errorMessage = err?.error?.error ?? 'Email ou mot de passe incorrect.';
      }
    });
  }
}
