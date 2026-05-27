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
    this.userService.login(this.email, this.password).subscribe({
      next: (response: any) => {
        const token = response.token;
        console.log('Token reçu:', response.token);
        localStorage.setItem('jwt_token', token);
        if(token.roles === 'ROLE_STUDENT' || token.roles === 'ROLE_TEACHER') {
          this.router.navigate(['/classes']);
        } else if(token.roles === 'ROLE_PARENT') {
          this.router.navigate(['/profil']);
        } else {
          this.router.navigate(['/profil']);
        } 
      },
      error: err => console.error('Login failed', err)
    });
  }
}
