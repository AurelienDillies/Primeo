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
  first_name: string = '';
  last_name: string = '';
  email: string = '';
  password: string = '';
  role: string = '';

  constructor(private userService: UserService, private router: Router) {}

  onSubmit() {
    const user = {
      first_name: this.first_name,
      last_name: this.last_name,
      email: this.email,
      password: this.password,
      roles: [this.role],
    };

    this.userService.register(user).subscribe({
      next: () => {
        console.log('Registration successful');
        this.router.navigate(['/utilisateurs']);
      },
      error: err => console.error('Registration failed', err)
    });
  }
}
