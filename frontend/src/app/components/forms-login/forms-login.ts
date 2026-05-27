import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { UserService } from '../../services/user-service';
import { Router } from '@angular/router';
import { jwtDecode } from 'jwt-decode';

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
        localStorage.setItem('jwt_token', token);

        const decoded: any = jwtDecode(token);

        console.log('Decoded token:', decoded);

        const roles = decoded.roles;

        if (roles.includes('ROLE_STUDENT') || roles.includes('ROLE_TEACHER')) {
          this.router.navigate(['/classes']);
        } else if (roles.includes('ROLE_PARENT')) {
          this.router.navigate(['/profil']);
        } else {
          this.router.navigate(['/profil']);
        }
      },
      error: err => console.error('Login failed', err)
    });
  }
}
