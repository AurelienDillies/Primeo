import { Component } from '@angular/core';
import { UserService } from '../../services/user-service';
import { Router } from '@angular/router';
import { FormsModule, NgForm } from '@angular/forms';

@Component({
  selector: 'app-forms-register',
  imports: [FormsModule],
  templateUrl: './forms-register.html',
  styleUrl: './forms-register.css',
})
export class FormsRegister {
  first_name: string = '';
  last_name: string = '';
  email: string = '';
  password: string = '';
  role: string = '';
  errorMessage = '';
  submitting = false;

  constructor(private userService: UserService, private router: Router) {}

  onSubmit(form: NgForm) {
    if (form.invalid || this.submitting) {
      this.errorMessage = 'Veuillez corriger les champs signalés avant de continuer.';
      form.control.markAllAsTouched();
      return;
    }

    const firstName = this.first_name.trim();
    const lastName = this.last_name.trim();
    const email = this.email.trim();
    const password = this.password;

    if (!firstName || !lastName || !email || password.length < 8 || !this.role) {
      this.errorMessage = 'Veuillez corriger les champs obligatoires.';
      return;
    }

    this.errorMessage = '';
    this.submitting = true;

    const user = {
      first_name: firstName,
      last_name: lastName,
      email,
      password,
      role: this.role,
    };

    this.userService.register(user).subscribe({
      next: () => {
        console.log('Registration successful');
        this.router.navigate(['/utilisateurs']);
      },
      error: (err) => {
        this.submitting = false;
        this.errorMessage = err?.error?.error
          ?? err?.error?.errors?.join(' ')
          ?? 'La création de l\'utilisateur a échoué.';
      },
    });
  }
}
