import { Component } from '@angular/core';
import { UserService } from '../../services/user-service';
import { Router } from '@angular/router';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-forms-register',
  imports: [FormsModule],
  templateUrl: './forms-register.html',
  styleUrl: './forms-register.css',
})
export class FormsRegister {
  firstname: string = '';
  lastname: string = '';
  email: string = '';
  password: string = '';
  role: string = '';

  constructor(private userService: UserService, private router: Router) {}

  onSubmit() {
    const user = {
      firstname: this.firstname,
      lastname: this.lastname,
      email: this.email,
      password: this.password,
      role: this.role,
    };

    this.userService.register(user).subscribe({
      next: () => {
        console.log('Registration successful');
        this.router.navigate(['/utilsateurs']);
      },
      error: err => console.error('Registration failed', err)
    });
  }
}
