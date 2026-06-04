import { Component } from '@angular/core';
import { FormsLogin } from '../../components/forms-login/forms-login';

@Component({
  selector: 'app-login',
  imports: [FormsLogin],
  templateUrl: './login.html',
  styleUrl: './login.css',
})
export class Login {}