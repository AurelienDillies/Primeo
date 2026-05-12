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
    console.log('Email:', this.email);
    console.log('Password:', this.password);
  }
}
